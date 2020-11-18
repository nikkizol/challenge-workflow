<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/agent")
 */
class AgentController extends AbstractController
{
    /**
     * @Route("/", name="agent", methods={"GET"})
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_AGENT');
        $userID = $this->getUser();

        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $tickets = $repository->findBy(
            ['status' => "OPEN", "handledBy" => null]
        );
        $myTickets = $repository->findBy(
            ["handledBy" => $userID]
        );
        return $this->render('ticketAgent/index.html.twig', [
            'tickets' => $tickets,
            'myTickets' => $myTickets
/*            'name' => $this->getUser()->getFirstName()*/
        ]);
    }

    /**
     * @Route("/{id}", name="ticketAgent_show", methods={"GET"})
     */
    public function show(Ticket $ticket): Response
    {
        return $this->render('ticketAgent/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ticketAgent_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ticket $ticket): Response
    {
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ticket_index');
        }

        return $this->render('ticketAgent/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/assign", name="ticketAgent_assign", methods={"GET","POST"})
     */
    public function assignAgent(Request $request, Ticket $ticket): Response
    {
        $userID = $this->getUser();
        $ticket = $ticket->setHandledBy($userID);

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('agent');

    }

}

