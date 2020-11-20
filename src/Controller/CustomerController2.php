<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\CommentRepository;
use App\Repository\TicketRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/customer")
 */
class CustomerController2 extends AbstractController
{
    /**
     * @Route("/", name="ticket_index", methods={"GET"})
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CUSTOMER');
        $userID = $this->getUser();

        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $tickets = $repository->findBy(
            ['createdBy' => $userID]
        );
        return $this->render('ticket/index.html.twig', [
            'tickets' => $tickets,
            'name' => $this->getUser()->getFirstName()
        ]);
    }

    /**
     * @Route("/new-ticket", name="ticket_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);
        $userID = $this->getUser();


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $ticket->setCreatedBy($userID);
            $ticket->setDatetime(new DateTime());
            $ticket->setStatus('OPEN');
            $ticket->setPriority(0);
            $ticket->setSecondLine(0);

            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('ticket_index');
        }

        return $this->render('ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ticket_show", methods={"GET"})
     */
    public function show(Ticket $ticket): Response
    {
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ticket_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ticket $ticket): Response
    {
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ticket_index');
        }

        return $this->render('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ticket_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Ticket $ticket): Response
    {
        if ($this->isCsrfTokenValid('delete' . $ticket->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ticket_index');
    }


    /**
     * @Route("/{id}/reopen", name="ticket_reopen", methods={"GET","POST"})
     */
    public function reopenTicket(Request $request, Ticket $ticket, TicketRepository $ticketRepository, CommentRepository $commentRepository): Response
    {

        $timeString = $ticket->getDatetime();
//        var_dump($timeString);
        $inTime = new DateTime();
//        var_dump($inTime);
        $interval = date_diff($timeString, $inTime);
        $diff = $interval->format("%H:%I:%S");
//        var_dump($diff);

        if ($diff < '01:00:00' && $ticket->getStatus() == 'CLOSED' ){
            $ticket->setStatus('OPEN');
            $ticket->setDatetime(new DateTime());
            $ticket->setHandledBy(null);
            $ticket->setSecondLine(0);
            $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('ticket_index');
        }
        return $this->redirectToRoute('ticket_index');
        }


}
