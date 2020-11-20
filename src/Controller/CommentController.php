<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Entity\User;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/ticket/{id}", name="comment_index", methods={"GET"})
     */
    public function index(CommentRepository $commentRepository, Ticket $ticket): Response
    {
        $ticketId = $ticket->getId();
        $comments = $commentRepository->findBy(
            ['ticketComment' => $ticketId, 'public' => 1]
        );

        return $this->render('comment/index.html.twig', [
            'comments' => $comments,
            'ticketId' => $ticketId,
        ]);
    }



    /**
     * @Route("/new/ticket/{id}", name="comment_new", methods={"GET","POST"})
     */
    public function new(Request $request, Ticket $ticket, MailerInterface $mailer): Response
    {
        $ticketId = $ticket->getId();
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        $userID = $this->getUser();
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $comment->setCreatedBy($userID);
            $comment->setTicketComment($ticket);
            $comment->setPublic(true);
            $comment->setDatetime(new DateTime());
//            if ($userID->getRoles()[0] == "ROLE_AGENT") {
//                $ticket->setStatus("WAITING FOR CUSTOMER FEEDBACK");
//            }
            if ($ticket->getStatus() == "WAITING FOR CUSTOMER FEEDBACK") {
                $ticket->setStatus("IN PROGRESS");
                $email = (new Email())
                    ->from('ticketDesk@example.com')
                    ->to($ticket->getHandledBy()->getUsername())
                    ->subject('Customer Replied!')
                    ->text('Customer Replied!')
                    ->html('<p>Customer Replied!</p>');
                $mailer->send($email);
            }
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('comment_index', ['id' => $ticketId]);
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
            'ticketId' => $ticketId,
        ]);
    }

    /**
     * @Route("/{id}", name="comment_show", methods={"GET"})
     */
    public function show(Comment $comment): Response
    {
//        $ticketId = $ticket->getId();
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
//            'ticketId' => $ticketId,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="comment_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comment_index');
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comment_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('comment_index');
    }
}
