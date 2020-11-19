<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/manager")
 */
class ManagerController extends AbstractController
{

    /**
     * @Route("/dashboard", name="manager", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        return $this->render('manager/index.html.twig', [
            'users' => $userRepository->findByRole(
                "ROLE_AGENT"
            )
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

}
