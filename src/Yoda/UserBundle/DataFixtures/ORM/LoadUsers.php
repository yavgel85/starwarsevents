<?php

namespace Yoda\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Yoda\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadUsers implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

//    /**
//     * Encode the password
//     *
//     * @param User $user
//     * @param $plainPassword
//     * @return mixed
//     */
//    private function encodePassword(User $user, $plainPassword)
//    {
//        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
//
//        return $encoder->encodePassword($plainPassword, $user->getSalt());
//    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('darth');
        //$user->setPassword($this->encodePassword($user, 'darthpass'));
        $user->setPlainPassword('darthpass');
        $user->setEmail('darth@deathstar.com');
        $manager->persist($user);

        $admin = new User();
        $admin->setUsername('wayne');
        //$admin->setPassword($this->encodePassword($admin, 'waynepass'));
        $admin->setPlainPassword('waynepass');
        $admin->setRoles(array('ROLE_ADMIN'));
        $admin->setEmail('wayne@deathstar.com');
        $manager->persist($admin);

        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}
