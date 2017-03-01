<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;
use Yoda\EventBundle\Entity\Event;
use Doctrine\ORM\EntityManager;
umask(0000);

$loader = require __DIR__.'/app/autoload.php';
Debug::enable();

require_once __DIR__.'/app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$kernel->boot();

$container = $kernel->getContainer();
$container->enterScope('request');
$container->set('request', $request);


$event = new Event();
$event->setName('Darth\'s surprise birthday party');
$event->setLocation('Deathstar');
$event->setTime(new \DateTime('tomorrow noon'));
//$event->setDetails('Ha! Darth HATES surprises!!!!');

$em = $container->get('doctrine')->getManager();

/*$em->persist($event);
$em->flush();

$templating = $container->get('templating');

echo $templating->render(
    'EventBundle:Default:index.html.twig',
    array(
        'name' => 'Yoda',
        'count' => 5,
    )
);*/

$user = $em
    ->getRepository('UserBundle:User')
    ->findOneBy(array('username' => 'wayne'))
;

foreach ($user->getEvents() as $event) {
    var_dump($event->getName());
}


// UPDATING PASSWORD
/*$wayne = $em
    ->getRepository('UserBundle:User')
    ->findOneByUsernameOrEmail('wayne');

$wayne->setPlainPassword('new');
$em->persist($user);
$em->flush();*/