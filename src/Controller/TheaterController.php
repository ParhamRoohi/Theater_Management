<?php


namespace App\Controller;

use App\Entity\Review;
use App\Entity\Theater;
use App\Entity\Ticket;
use App\Form\TheaterType;
use App\Repository\ReviewRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TheaterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TheaterController extends AbstractController
{
    #[Route('/', name: 'theater_index')]
    public function index(TheaterRepository $theaterRepository): Response
    {
        $theaters = $theaterRepository->findAll();

        return $this->render('theater/index.html.twig', [
            'theaters' => $theaters,
        ]);
    }

    #[Route('/theater/new', name: 'theater_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $theater = new Theater();
        $form = $this->createForm(TheaterType::class, $theater);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($theater);
            $entityManager->flush();

            return $this->redirectToRoute('theater_show', ['id' => $theater->getId()]);
        }

        return $this->render('theater/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
  
#[Route('/theater/{id}/comments', name: 'theater_comments')]
#[IsGranted('ROLE_ADMIN')]
public function commentView(int $id, EntityManagerInterface $entityManager, ReviewRepository $reviewRepository): Response
{

    $theater = $entityManager->getRepository(Theater::class)->find($id);

    if (!$theater) {
        throw $this->createNotFoundException('The theater does not exist');
    }
    $comments = $reviewRepository->findBy(['theaterName' => $theater]);

    return $this->render('theater/comment.html.twig', [
        'theater' => $theater,
        'comments' => $comments,
    ]);
}

    #[Route('/theater/{id}/edit', name: 'theater_edit')]
    public function edit(Request $request, Theater $theater, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TheaterType::class, $theater);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('theater_index', ['id' => $theater->getId()]);
        }

        return $this->render('theater/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/theater/{id}', name: 'theater_show')] 
    public function show(Theater $theater, EntityManagerInterface $entityManager): Response 
    { 
        $purchasedTicketsCount = $entityManager->getRepository(Ticket::class) 
            ->count(['show' => $theater]); 
     
        $availableSeats = $theater->getCapacity() - $purchasedTicketsCount; 
     
        return $this->render('theater/show.html.twig', [ 
            'theater' => $theater, 
            'availableSeats' => $availableSeats, 
        ]); 
    }

    #[Route('/theater/{id}/delete', name: 'theater_delete', methods: ['POST'])]
    public function delete(Request $request, Theater $theater, EntityManagerInterface $entityManager, TicketRepository $ticketRepository, ReviewRepository $reviewRepository): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $theater->getId(), $request->request->get('_token'))) {
            throw new AccessDeniedException('Invalid CSRF token.');
        }
        $tickets = $ticketRepository->findBy(['show' => $theater]);
        foreach ($tickets as $ticket) {
            $entityManager->remove($ticket);
        }
        $reviews = $reviewRepository->findBy(['theaterName' => $theater]);
        foreach ($reviews as $review) {
            $entityManager->remove($review);
        }
    
        $entityManager->remove($theater);
        $entityManager->flush();

        $this->addFlash('success', 'Theater deleted successfully.');

        return $this->redirectToRoute('theater_index');
    }
    #[Route('/theater/tickets/{id}', name: 'theater_tickets')] 
    #[IsGranted('ROLE_ADMIN')] 
    public function viewTickets(int $id, EntityManagerInterface $entityManager,TicketRepository $ticketRepository): Response 
    { 
        $theater = $entityManager->getRepository(Theater::class)->find($id); 
        if (!$theater) { 
            throw $this->createNotFoundException(); 
        } 
 
        $tickets = $ticketRepository->findBy(['show' => $theater]);
 
        return $this->render('theater/tickets.html.twig', [ 
            'tickets' => $tickets, 
        ]); 
    }

}

