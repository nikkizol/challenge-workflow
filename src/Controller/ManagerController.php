<?php

namespace App\Controller;

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
     * @Route("/display-agents", name="display_agents", methods={"GET"})
     */
    public function showAgents(UserRepository $userRepository)
    {

        return $this->render('manager/display_agents.html.twig', [
            'users' => $userRepository->findByRole(
                "ROLE_AGENT"
            )
        ]);
    }

}
