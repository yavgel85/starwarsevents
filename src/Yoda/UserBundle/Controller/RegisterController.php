<?php

namespace Yoda\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Yoda\EventBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Yoda\UserBundle\Entity\User;
use Yoda\UserBundle\Form\RegisterFormType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RegisterController extends Controller
{
    /**
     * User registration
     *
     * @param Request $request
     * @Route("/register", name="user_register")
     * @Template()
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function registerAction(Request $request)
    {
        $defaultUser = new User();
        $defaultUser->setUsername('Chewbacca');

        $form = $this->createForm(RegisterFormType::class, $defaultUser);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $user = $form->getData();
            /*$user->setPassword(
                $this->encodePassword($user, $user->getPlainPassword())
            );*/
            $user->setPlainPassword($user->getPlainPassword());

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Welcome to the Death Star, have a magical day!');

            $this->authenticateUser($user);

            return $this->redirectToRoute('event_index');
        }

        return ['form' => $form->createView()];
    }

//    /**
//     * Password encode
//     *
//     * @param User $user
//     * @param $plainPassword
//     * @return string
//     */
//    private function encodePassword(User $user, $plainPassword)
//    {
//        $encoder = $this->container->get('security.encoder_factory')
//            ->getEncoder($user);
//
//        return $encoder->encodePassword($plainPassword, $user->getSalt());
//    }

    /**
     * Automatically Authenticating after Registration
     *
     * @param User $user
     */
    private function authenticateUser(User $user)
    {
        $providerKey = 'secured_area'; // your firewall name

        $key = '_security.'.$providerKey.'.target_path';
        $session = $this->getRequest()->getSession();

        // get the URL to the last page, or fallback to the homepage
        if ($session->has($key)) {
            $url = $session->get($key);
             $session->remove($key);
         } else {
            $url = $this->generateUrl('event_index');
        }

        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

        $this->getSecurityContext()->setToken($token);
    }
}