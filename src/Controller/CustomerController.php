<?php

namespace App\Controller;

use App\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    /**
     * @Route("/customer", name="customer")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CUSTOMER');
        $userID = $this->getUser();

        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $tickets = $repository->findBy(
            ['createdBy' => $userID]
        );
        return $this->render('customer/index.html.twig', [
            'tickets' => $tickets,
            'name' => $this->getUser()->getFirstName()
        ]);
    }

}
