<?php

namespace Yoda\EventBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yoda\EventBundle\Entity\Event;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Event controller.
 */
class EventController extends Controller
{
    /**
     * @Template()
     * @Route("/", name="event")
     */
    public function indexAction()
    {
        return [];
    }

//    /**
//     * Lists all event entities.
//     * @Template()
//     */
//    public function indexAction()
//    {
//        $em = $this->getDoctrine()->getManager();
//        //$entities = $em->getRepository('EventBundle:Event')->findAll();
//
//        $entities = $em
//            ->getRepository('EventBundle:Event')
//            ->getUpcomingEvents()
//        ;
//
//        return ['entities' => $entities];
//    }

    /**
     * Creates a new event entity.
     * @Template()
     *
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newAction(Request $request)
    {
        $this->enforceUserSecurity('ROLE_EVENT_CREATE');

        $event = new Event();
        $form = $this->createForm('Yoda\EventBundle\Form\EventType', $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $event->setOwner($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush($event);

            return $this->redirectToRoute('event_show', [
                'id' => $event->getId(),
            ]);
        }

        return [
            'event' => $event,
            'form' => $form->createView(),
        ];
    }

    /**
     * @param Request $request
     */
    public function createAction(Request $request)
    {
        $this->enforceUserSecurity('ROLE_EVENT_CREATE');
    }

    /**
     * Finds and displays a event entity.
     * @Template()
     *
     * @param Event $entity
     * @return array
     */
    public function showAction(Event $entity)
    {
        $deleteForm = $this->createDeleteForm($entity);

        return [
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing event entity.
     * @Template()
     *
     * @param Request $request
     * @param Event $entity
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Request $request, Event $entity)
    {
        $this->enforceUserSecurity();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }
        $this->enforceOwnerSecurity($entity);

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createForm('Yoda\EventBundle\Form\EventType', $entity);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('event_edit', [
                'id' => $entity->getId(),
            ]);
        }

        return [
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Deletes a event entity.
     *
     */
    public function deleteAction(Request $request, Event $event)
    {
        $this->enforceUserSecurity();

        if (!$event) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }
        $this->enforceOwnerSecurity($event);

        $form = $this->createDeleteForm($event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush($event);
        }

        return $this->redirectToRoute('event_index');
    }

    /**
     * Creates a form to delete a event entity.
     *
     * @param Event $event The event entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Event $event)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('event_delete', ['id' => $event->getId()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }


    /**
     * Check the user
     * @param string $role
     */
    private function enforceUserSecurity($role = 'ROLE_USER')
    {
        $this->denyAccessUnlessGranted($role, null, 'Need '.$role);
    }

    private function enforceOwnerSecurity(Event $event)
    {
        $user = $this->getUser();

        if ($user != $event->getOwner()) {
            throw $this->createAccessDeniedException('You are not the owner!!!');
        }
    }

    /**
     * @param Event $event
     * @param string $format
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function createAttendingResponse(Event $event, $format)
    {
        if ($format == 'json') {
            $data = [
                'attending' => $event->hasAttendee($this->getUser())
            ];

            $response = new JsonResponse($data);

            return $response;
        }

        return $this->redirectToRoute('event_show', [
            'slug' => $event->getSlug(),
        ]);
    }

    /**
     * @param $id
     * @param $format
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function attendAction($id, $format)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var $event \Yoda\EventBundle\Entity\Event */
        $event = $em->getRepository('EventBundle:Event')->find($id);

        if (!$event) {
            throw $this->createNotFoundException('No event found for id '.$id);
        }

        if (!$event->hasAttendee($this->getUser())) {
            $event->getAttendees()->add($this->getUser());
        }

        $em->persist($event);
        $em->flush();

        return $this->createAttendingResponse($event, $format);
    }

    /**
     * @param $id
     * @param $format
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function unattendAction($id, $format)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var $event \Yoda\EventBundle\Entity\Event */
        $event = $em->getRepository('EventBundle:Event')->find($id);

        if (!$event) {
            throw $this->createNotFoundException('No event found for id '.$id);
        }

        if ($event->hasAttendee($this->getUser())) {
            $event->getAttendees()->removeElement($this->getUser());
        }

        $em->persist($event);
        $em->flush();

        return $this->createAttendingResponse($event, $format);
    }

    /**
     * It makes queries for upcoming events, and renders a template
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function _upcomingEventsAction($max = null)
    {
        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository('EventBundle:Event')
            ->getUpcomingEvents($max)
        ;

        return $this->render('EventBundle:Event:_upcomingEvents.html.twig', [
            'events' => $events,
        ]);
    }

}
