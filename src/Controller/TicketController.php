<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\Theater;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TicketController extends AbstractController
{
    
#[Route('/ticket/buy/{id}', name: 'ticket_buy', methods: ['GET', 'POST'])]
    public function buyTicket(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $theater = $entityManager->getRepository(Theater::class)->find($id);
        if (!$theater) {
            throw $this->createNotFoundException('The theater does not exist.');
        }

        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedException('You must be logged in to buy a ticket.');
        }

        $purchasedTicketsCount = $entityManager->getRepository(Ticket::class)
            ->count(['show' => $theater]);

        $availableSeats = $theater->getCapacity() - $purchasedTicketsCount;

        if ($request->isMethod('POST')) {
            $quantity = $request->request->get('quantity', 1);
            $quantity = max(1, min(10, (int) $quantity));

            if ($quantity > $availableSeats) {
                $this->addFlash('error', 'Not enough seats available. You can only buy ' . $availableSeats . ' more ticket(s).');
                return $this->redirectToRoute('ticket_buy', ['id' => $id]);
            }

            for ($i = 0; $i < $quantity; $i++) {
                $ticket = new Ticket();
                $ticket->setShow($theater);
                $ticket->setCustomer($user);
                $ticket->setPurchaseDate(new \DateTimeImmutable());
                $ticket->setSeatNumber(rand(1, $theater->getCapacity()));
                $ticket->setPrice($theater->getPrice());

                if ($purchasedTicketsCount + $quantity >= $theater->getCapacity()) {
                    $ticket->setStatus('full');
                } else {
                    $ticket->setStatus('valid');
                }

                $entityManager->persist($ticket);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Tickets bought successfully.');

            return $this->redirectToRoute('theater_index', ['id' => $id]);
        }

        return $this->render('ticket/buy.html.twig', [
            'theater' => $theater,
        ]);
    }

    
}
