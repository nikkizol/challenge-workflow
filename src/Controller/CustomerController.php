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
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $tickets = $repository->findBy(
            ['id' => 'ASC']
        );
        return $this->render('customer/index.html.twig', [
            'tickets' => $tickets
        ]);
    }

}
