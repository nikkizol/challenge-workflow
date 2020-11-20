<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Form\CommentAgentType;
use App\Form\ManagerTicketType;
use App\Form\ManagerType;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/manager")
 */
class ManagerController extends AbstractController
{

    /**
     * @Route("/dashboard", name="manager", methods={"GET"})
     * @param UserRepository $userRepository
     * @param Ticket $ticket
     * @return Response
     */
    public function index(TicketRepository $ticketRepository, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');
        $agents = $userRepository->findByRole("ROLE_AGENT");
        $agentsSec = $userRepository->findByRole("ROLE_SECOND_AGENT");

        $ticket = $ticketRepository->findBy(["status" => ['OPEN','WAITING FOR CUSTOMER FEEDBACK','IN PROGRESS']]);

        return $this->render('manager/index.html.twig', [
            'users' => $agents,
            'agentsSec' => $agentsSec,
            "tickets" => $ticket
        ]);
    }

    /**
     * @Route("/display-agents", name="display_agent", methods={"GET"})
     */
    public function showAgent(UserRepository $userRepository): Response
    {
        $agent = $userRepository->findByRole('ROLE_AGENT');

        $userID = $_GET["id"];

        $userRepository = $this->getDoctrine()->getRepository(Ticket::class);
        $tickets = $userRepository->findBy(
            ['status' => "OPEN", "handledBy" => null]
        );
        $myTickets = $userRepository->findBy(
            ["handledBy" => $userID]
        );
        return $this->render('manager/manager_agent_view.html.twig', [
            'tickets' => $tickets,
            'myTickets' => $myTickets
            /*            'name' => $this->getUser()->getFirstName()*/
        ]);
    }

    /**
     * @Route("/edit-ticket/{id}", name="edit_ticket", methods={"GET","POST"})
     */
    public function editTicket(Request $request, Ticket $ticket): Response
    {
        $ticket->setStatus("WON'T FIX");
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('manager');
    }



    /**
     * @Route("/edit-ticket/{id}", name="edit_ticket", methods={"GET","POST"})
     */
    public function new(Request $request, Ticket $ticket): Response
    {
        $ticketId = $ticket->getId();
        $userID = $this->getUser();
        $comment = new Comment();
        $form = $this->createForm(ManagerType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $ticket->setStatus("WON'T FIX");
            $comment->setCreatedBy($userID);
            $comment->setPublic(0);
            $comment->setTicketComment($ticket);
            $comment->setDatetime(new DateTime());
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('manager');
        }
        return $this->render('commentAgent/new.html.twig', [
            'form' => $form->createView(),
            'ticketId' => $ticketId,
        ]);
    }







    /**
     * @Route("/reset-tickets", name="reset_tickets", methods={"GET","POST"})
     */
    public function resetTickets(TicketRepository $ticketRepository): Response
    {
        $tickets = $ticketRepository->findBy(["status" => ['OPEN','WAITING FOR CUSTOMER FEEDBACK','IN PROGRESS']]);
        foreach ($tickets as $ticket){
            $ticket->setStatus("OPEN");
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->redirectToRoute('manager');

    }
    
}
