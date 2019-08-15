<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use Symfony\Contracts\Translation\TranslatorInterface;

use App\Repository\FightRepository;
use App\Repository\FightBetRepository;

use App\Entity\Fight;
use App\Entity\FightBet;

use App\Service\ImportUploader;

use App\Form\FightFormType;
use App\Form\FightsImportFormType;

class FightsController extends AbstractController
{
    private $csrfTokenManager;
    private $translator;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, TranslatorInterface $translator)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->translator = $translator;
    }

    /**
     * @Route("/", defaults={"page": "1"}, methods={"GET"}, name="fights_index")
     * @Route("/fights/page/{page}", methods={"GET"}, name="fights_index_paginated", requirements={"page"="[1-9]\d*"})
     */
    public function index (int $page, FightRepository $fights): Response {
        $paginator = $fights->findLatest($page);

        return $this->render('fights/index.html.twig', [
            'optionParticipantValue' => FightBet::OPTION_PARTICIPANT,
            'optionOpponentValue' => FightBet::OPTION_OPPONENT,

            'paginator' => $paginator
        ]);
    }
    
    /**
     * @Route("/fight/{id}/bets", defaults={"page": "1"}, methods={"GET"}, name="fight_bets", requirements={"id"="\d+"})
     * @Route("/fight/{id}/bets/page/{page}", methods={"GET"}, name="fight_bets_paginated", requirements={"id"="\d+","page"="[1-9]\d*"})
     */
    public function bets (int $id, int $page, FightBetRepository $bets): Response {
        $fight = $this->getDoctrine()
            ->getRepository(Fight::class)
            ->find($id);

        if (!$fight) {
            throw $this->createNotFoundException(
                'No fight found for id '.$id
            );
        }

        if ($fight->getStatus() === Fight::STATUS_CREATED) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'You cannot view bets if fight has not started!');
        }

        $paginator = $bets->findLatestByFight($fight, $page);
 
        return $this->render('fight/bets.html.twig', [
            'fight' => $fight,
            'paginator' => $paginator
        ]);
    }

    /**
     * @Route("/fight/{id}/bet/{option}", methods={"POST"}, name="fight_bet", requirements={"id"="\d+","option"="(0|1)"})
     * @IsGranted("ROLE_USER")
     */
    public function bet(int $id, int $option, Request $request): Response
    {
        $token = new CsrfToken('bet', $request->request->get('_csrf_token'));

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $fight = $this->getDoctrine()
            ->getRepository(Fight::class)
            ->find($id);

        if (!$fight) {
            throw $this->createNotFoundException(
                'No fight found for id '.$id
            );
        }

        if ($fight->getStatus() !== Fight::STATUS_CREATED) {
            throw $this->createAccessDeniedException('You cannot bet if fight was started or ended!');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $bet = $this->getDoctrine()
            ->getRepository(FightBet::class)
            ->findOneBy([
                'fight' => $fight,
                'user' => $user
            ]);
        
        if ($bet) {
            $bet->setOption($option);

            $this->addFlash('success', 'Your bet has been updated!');
        } else {
            $bet = new FightBet();

            $bet->setFight($fight);
            $bet->setUser($user);

            $bet->setOption($option);

            $entityManager->persist($bet);

            $this->addFlash('success', 'Your bet has been added!');
        }

        $entityManager->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/fights/admin", defaults={"page": "1"}, methods={"GET"}, name="fights_admin")
     * @Route("/fights/admin/page/{page}", methods={"GET"}, name="fights_admin_paginated", requirements={"page"="[1-9]\d*"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function admin (int $page, FightRepository $fights): Response {
        $paginator = $fights->findLatest($page);

        return $this->render('fights/admin.html.twig', [
            'paginator' => $paginator
        ]);
    }

    /**
     * @Route("/fight/{id}/status/{status}", methods={"POST"}, name="fight_status", requirements={"id"="\d+","status"="(created|started|ended)"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function status(int $id, string $status, Request $request): Response
    {
        $token = new CsrfToken('admin', $request->request->get('_csrf_token'));

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $fight = $this->getDoctrine()
            ->getRepository(Fight::class)
            ->find($id);

        if (!$fight) {
            throw $this->createNotFoundException(
                'No fight found for id '.$id
            );
        }

        if ($status === Fight::STATUS_ENDED) {
            $winner = (int) $request->request->get('winner');

            if (!in_array($winner, FightBet::OPTION_CHOICES, true)) {
                throw new BadRequestHttpException('Invalid winner option!');
            }
        }

        $bets = $fight->getBets();

        foreach ($bets as $bet) {
            $option = $bet->getOption();

            $user = $bet->getUser();

            $points = $user->getPoints();

            if ($status === Fight::STATUS_ENDED && isset($winner)) {
                if ($option === $winner) {
                    $points += 1;
                } else {
                    $points -= 1;
                }
            } else if ($fight->getWinner() !== null) {
                if ($option === $fight->getWinner()) {
                    $points -= 1;
                } else {
                    $points += 1;
                }
            }

            if ($points < 0) {
                $points = 0;
            }

            $user->setPoints($points);
        }

        if ($status === Fight::STATUS_ENDED && isset($winner)) {
            $fight->setWinner($winner);
        } else {
            $fight->setWinner(null);
        }

        $fight->setStatus($status);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->flush();
        
        $this->addFlash('success', 'Fight status was updated successfully!');

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/fight/new", methods={"GET", "POST"}, name="fight_new")
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $fight = new Fight();
        
        $form = $this->createForm(FightFormType::class, $fight);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fight->setStatus(Fight::STATUS_CREATED);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($fight);
            $entityManager->flush();

            $this->addFlash('success', 'The fight was successfully created!');

            return $this->redirect($request->getUri());
        }

        return $this->render('fight/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/fights/import", methods={"GET", "POST"}, name="fights_import")
     * @IsGranted("ROLE_ADMIN")
     */
    public function import(Request $request, ImportUploader $importUploader): Response
    {
        $form = $this->createForm(FightsImportFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $importFile */
            $importFile = $form['file']->getData();

            if ($importFile) {
                $importedFightsCount = $importUploader->import($importFile);

                $this->addFlash(
                    'success',
                    $this->translator->trans('%count% fights were successfully imported!', ['%count%' => $importedFightsCount])
                );
            }

            return $this->redirect($request->getUri());
        }

        return $this->render('fights/import.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
