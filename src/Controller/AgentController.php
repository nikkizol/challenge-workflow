<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Entity\User;
use App\Form\CommentAgentType;
use App\Form\TicketType;
use App\Repository\CommentRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use DateTime;
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
            ["handledBy" => $userID, 'status' => "IN PROGRESS",'status' => "WAITING FOR CUSTOMER FEEDBACK" ]
        );

        $closeTickets = $repository->findBy(
            ["status" => "CLOSED"]
        );

        return $this->render('ticketAgent/index.html.twig', [
            'tickets' => $tickets,
            'myTickets' => $myTickets,
            'closeTickets' => $closeTickets
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
        $ticket->setHandledBy($userID);
        $ticket->setStatus('IN PROGRESS');

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('agent');

    }

    /**
     * @Route("/{id}/close", name="ticketAgent_close", methods={"GET","POST"})
     */
    public function toCloseTicket(Request $request, Ticket $ticket, TicketRepository $ticketRepository, CommentRepository $commentRepository): Response
    {

        $ticketId = $ticket->getId();
        $commentOnTicket = $commentRepository->findBy(['ticketComment' => $ticketId]);
        if (isset($commentOnTicket[0])) {
            $whoLeftcomment = $commentOnTicket[0]->getCreatedBy()->getRoles();
            if ($ticket->getStatus() == 'IN PROGRESS' && $whoLeftcomment[0] == 'ROLE_AGENT') {
                $ticket->setStatus('CLOSED');
                $ticket->setDatetime(new DateTime());
                $ticket->setHandledBy(null);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('agent');
            }
        }
        return $this->redirectToRoute('agent');
    }


    /**
     * @Route("/ticket/{id}", name="commentAgent_index", methods={"GET"})
     */
    public function indexAgent(CommentRepository $commentRepository, Ticket $ticket): Response
    {
        $ticketId = $ticket->getId();
        var_dump($ticketId);
        $comments = $commentRepository->findBy(
            ['ticketComment' => $ticketId]
        );

        return $this->render('commentAgent/index.html.twig', [
            'comments' => $comments,
            'ticketId' => $ticketId,
        ]);
    }

    /**
     * @Route("/new/ticket/{id}", name="commentAgent_new", methods={"GET","POST"})
     */
    public function new(Request $request, Ticket $ticket): Response
    {
        $ticketId = $ticket->getId();
        $comment = new Comment();
        $form = $this->createForm(CommentAgentType::class, $comment);
        $form->handleRequest($request);
        $userID = $this->getUser();
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $comment->setCreatedBy($userID);
            $comment->setTicketComment($ticket);
//            $comment->setPublic(true);
            $comment->setDatetime(new DateTime());
            if ($comment->getPublic() == 1) {
                $ticket->setStatus("WAITING FOR CUSTOMER FEEDBACK");
            }
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('commentAgent_index', ['id' => $ticketId]);
        }

        return $this->render('commentAgent/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
            'ticketId' => $ticketId,
        ]);
    }

    /**
     * @Route("/comment/{id}", name="commentAgent_show", methods={"GET"})
     */
    public function showAgent(Comment $comment): Response
    {
//        $ticketId = $ticket->getId();
        return $this->render('commentAgent/show.html.twig', [
            'comment' => $comment,
//            'ticketId' => $ticketId,
        ]);
    }

    /**
     * @Route("/comment/{id}/edit", name="commentAgent_edit", methods={"GET","POST"})
     */
    public function editAgent(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(CommentAgentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('commentAgent_index');
        }

        return $this->render('commentAgent/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="commentAgent_delete", methods={"DELETE"})
     */
    public function deleteAgent(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('commentAgent_index');
    }

}

