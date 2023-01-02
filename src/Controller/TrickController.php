<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tricks', name: 'trick_')]
final class TrickController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(TrickRepository $trickRepository): Response
    {
        return $this->render('trick/list.html.twig', [
            'tricks' => $trickRepository->findAll()
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(): Response
    {

    }

    #[Route('/{slug}/read', name: 'read', methods: ['GET'])]
    public function read(): Response
    {

    }

    #[Route('/{slug}/update', name: 'update', methods: ['GET', 'POST'])]
    public function update(): Response
    {

    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(): Response
    {

    }
}