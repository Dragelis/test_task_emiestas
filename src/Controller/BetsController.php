<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Response;

use App\Repository\UserRepository;

class BetsController extends AbstractController
{
    /**
     * @Route("/bets/top", defaults={"page": "1"}, methods={"GET"}, name="bets_top")
     * @Route("/bets/top/{page}", methods={"GET"}, name="bets_top_paginated", requirements={"page"="[1-9]\d*"})
     */
    public function index (int $page, UserRepository $users): Response {
        $paginator = $users->findLatestAndSortByPoints($page);

        return $this->render('bets/top.html.twig', [
            'paginator' => $paginator
        ]);
    }
}
