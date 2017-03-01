<?php

namespace Yoda\UserBundle\Controller;

//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Yoda\EventBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SecurityController extends  Controller
{
    /**
     * Login user
     *
     * @Route("/login", name="login_form")
     * @Template()
     *
     * @param Request $request
     * @return array
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        // get the login error if there is one
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);

        return [
            // last username entered by the user
            'last_username' => $session->get(SecurityContextInterface::LAST_USERNAME),
            'error'         => $error,
        ];
    }

    /**
     * Check the login & password
     *
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {
    }

    /**
     * Logout user
     *
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
    }

}
