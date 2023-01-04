<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\User;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $entityManager, string $uploadDir): Response
    {
        $trick = new Trick();

        /** @var User $user */
        $user = $this->getUser();

        $trick->setUser($user);

        $form = $this->createForm(
            TrickType::class,
            $trick,
            ['validation_groups' => ['Default', 'create']]
        )->handleRequest($request);

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
    #[IsGranted('edit', subject: 'trick')]
    public function update(Trick $trick, Request $request, EntityManagerInterface $entityManager, string $uploadDir): Response
    {
        $form = $this->createForm(TrickType::class, $trick)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($trick->getImageFile() !== null) {
                $trick->setImage(
                    sprintf(
                        '%s.%s',
                        Uuid::v4(),
                        $trick->getImageFile()->getClientOriginalExtension()
                    )
                );
                $trick->getImageFile()->move($uploadDir, $trick->getImage());
            }
            $entityManager->flush();
            return $this->redirectToRoute('trick_read', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/update.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('edit', subject: 'trick')]
    public function delete(Trick $trick, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($trick);
        $entityManager->flush();
        return $this->redirectToRoute('trick_list');
    }
}