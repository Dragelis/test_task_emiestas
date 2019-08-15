<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Repository\FightBetRepository;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile/bets", defaults={"page": "1"}, methods={"GET"}, name="bets_index")
     * @Route("/profile/bets/page/{page}", methods={"GET"}, name="bets_index_paginated", requirements={"page"="[1-9]\d*"})
     * @IsGranted("ROLE_USER")
     */
    public function bets (int $page, FightBetRepository $bets): Response {
        $paginator = $bets->findLatestByUser($this->getUser(), $page);

        return $this->render('profile/bets.html.twig', [
            'paginator' => $paginator
        ]);
    }
}
