<?php

namespace App\Controller;

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
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        return $this->render('manager/index.html.twig', [
            'controller_name' => 'ManagerController',
        ]);
    }
}
