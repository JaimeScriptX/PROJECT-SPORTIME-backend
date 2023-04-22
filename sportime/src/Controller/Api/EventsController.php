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



class EventsController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/events")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function getEvents(
        EventsRepository $eventsRepository
    ) {
        return $eventsRepository->findAll();
        
    }

    /**
     * @Rest\Post(path="/events")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
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



}