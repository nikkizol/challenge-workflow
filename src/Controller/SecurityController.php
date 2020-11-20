<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser() !== null) {
             if ($this->getUser()->getRoles()[0] == "ROLE_CUSTOMER") {
                 return $this->redirectToRoute('ticket_index');
             }
             elseif ($this->getUser()->getRoles()[0] == "ROLE_AGENT") {
                 return $this->redirectToRoute('agent');
             }
             elseif ($this->getUser()->getRoles()[0] == "ROLE_SECOND_AGENT") {
                 return $this->redirectToRoute('second-agent');
             }
             elseif ($this->getUser()->getRoles()[0] == "ROLE_MANAGER") {
                 return $this->redirectToRoute('manager');
             }
         }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
