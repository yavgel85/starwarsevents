<?php

namespace Yoda\EventBundle\Reporting;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Router;

class EventReportManager
{
    //  the entity manager object
    private $em;
    private $router;

    /**
     * EventReportManager constructor.
     * @param EntityManager $em
     * @param Router $router
     */
    public function __construct(EntityManager $em, Router $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    public function getRecentlyUpdatedReport()
    {
        $events = $this->em->getRepository('EventBundle:Event')->getRecentlyUpdatedEvents();

        $rows = [];
        foreach ($events as $event) {
            $data = [
                $event->getId(),
                $event->getName(),
                $event->getTime()->format('Y-m-d H:i:s'),
                $this->router->generate('event_show', ['slug' => $event->getSlug()], true),
            ];

            $rows[] = implode(',', $data);
        }

        return implode("\n", $rows);
    }
}