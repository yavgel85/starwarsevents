<?php

namespace Yoda\UserBundle\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Yoda\UserBundle\Entity\User;

class UserListener
{
    private $encoderFactory;

    public function __construct(EncoderFactory $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof User) {
            $this->handleEvent($entity);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        //die('UUPPPPPDDAAAAAATING!');

        $entity = $args->getEntity();
        if ($entity instanceof User) {
            $this->handleEvent($entity);
        }
    }

    /**
     * Encode the password
     *
     * @param User $user
     */
    private function handleEvent(User $user)
    {
        if (!$user->getPlainPassword()) {
            return;
        }

        $plainPassword = $user->getPlainPassword();
        $encoder = $this->encoderFactory->getEncoder($user);

        $password = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($password);
    }
}