<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\Theater;
use App\Form\ReviewFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ReviewController extends AbstractController
{
    #[Route('/review/new/{id}', name: 'theater_add_review', methods: ['GET', 'POST'])]
    public function addReview(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $theater = $entityManager->getRepository(Theater::class)->find($id);
        if (!$theater) {
            throw new AccessDeniedException('You must be logged in to leave a review.');
        }

        $review = new Review();
        $review->setTheaterName($theater);
        $review->setAuthor($this->getUser());
        $review->setCreatedAt(new \DateTimeImmutable());
        $review->setUpdatedAt(new \DateTimeImmutable());


        $form = $this->createForm(ReviewFormType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($review);
            $entityManager->flush();

            $this->addFlash('success', 'Comment added successfully.');

            return $this->redirectToRoute('theater_index');
        }

        return $this->render('review/add.html.twig', [
            'theater' => $theater,
            'reviewForm' => $form->createView(),
        ]);
    }
}