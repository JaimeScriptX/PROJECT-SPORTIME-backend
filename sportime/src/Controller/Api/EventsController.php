<?php

namespace App\Controller\Api;

use App\Repository\EventsRepository;

use App\Service\EventsManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Events;
use App\Form\Type\EventsFormType;
use App\Service\EventsFormProcessor;

class EventsController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/events")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(
        EventsManager $eventsManager
    ) {
        return $eventsManager->getRepository()->findAll();
        
    }

    /**
     * @Rest\Post(path="/events")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function postAction(
        EventsManager $eventsManager,
        EventsFormProcessor $eventsFormProcessor,
        Request $request
    ) {
        $events = $eventsManager->create();
        [$events, $error] = $eventsFormProcessor($events, $request);
        $statusCode = $events ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;
        $data = $events ?? $error;
        return View::create($data, $statusCode);
    }

    /**
     * @Rest\Put(path="/event/{id}")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function putAction(
        int $id,
        EventsManager $eventsManager,
        EventsFormProcessor $eventsFormProcessor,
        Request $request
    ) {
       $events = $eventsManager->find($id);
       if (!$events){
        return View::create("Event not found", Response::HTTP_BAD_REQUEST);
       }
        [$events, $error] = $eventsFormProcessor($events, $request); 
        $statusCode = $events ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;
        $data = $events ?? $error;
        return View::create($data, $statusCode);
    }

    /**
    * @Rest\Delete(path="/events/{id}")
    * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
    */
    public function deleteAction(
        int $id,
        EventsManager $eventsManager,
        EventsFormProcessor $eventsFormProcessor,
        Request $request
    ) {
        $events = $eventsManager->find($id);
        if (!$events){
            return View::create("Event not found", Response::HTTP_BAD_REQUEST);
        }
        $eventsManager->delete($events);
        return View::create(null, Response::HTTP_NO_CONTENT);
    }



}

/*
public function postEvents(
        Request $request,
        EntityManagerInterface $em
    ) {
        $events = new Events();
        $form = $this->createForm(EventsFormType::class, $events);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($events);
            $em->flush();
            return $events;
        }

        return $form;
    }
*/