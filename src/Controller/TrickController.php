<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

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
    public function create(Request $request, EntityManagerInterface $entityManager, string $uploadDir): Response
    {
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setCreatedAt(new DateTimeImmutable());
            $trick->setImage(
                sprintf(
                    '%s.%s',
                    Uuid::v4(),
                    $trick->getImageFile()->getClientOriginalExtension()
                )
            );
            $trick->getImageFile()->move($uploadDir, $trick->getImage());
            $entityManager->persist($trick);
            $entityManager->flush();
            return $this->redirectToRoute('trick_read', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{slug}/read', name: 'read', methods: ['GET'])]
    public function read(Trick $trick): Response
    {
        return $this->render('trick/read.html.twig', [
            'trick' => $trick
        ]);
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