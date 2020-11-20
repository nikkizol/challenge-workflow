<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Form\ManagerTicketType;
use App\Repository\UserRepository;
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
    public function index(Ticket $ticket, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');
        $agents = $userRepository->findByRole("ROLE_AGENT");
        $ticket = $userRepository->findBy(
            ['status' => "OPEN", "WAITING FOR CUSTOMER FEEDBACK", "IN PROGRESS"]);

        return $this->render('manager/index.html.twig', [
            'users' => $agents,
            "ticket" => $ticket
        ]);
    }

    /**
     * @Route("/display-agents", name="display_agent", methods={"GET"})
     */
    public function showAgent(UserRepository $userRepository): Response
    {
        $agent = $userRepository->findByRole("ROLE_AGENT");

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
        $ticket->setStatus("won't fix");
        
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('manager');
    }

    /**
     * @Route("/reset-tickets", name="reset_tickets", methods={"GET","POST"})
     */
    public function resetTickets(Ticket $ticket, UserRepository $userRepository): Response
    {
        $tickets = $userRepository->findBy(
            ['status' => "OPEN", "WAITING FOR CUSTOMER FEEDBACK", "IN PROGRESS"]);

        $ticket->setStatus("OPEN");

        $this->getDoctrine()->getManager()->flush();

    }
    
}
