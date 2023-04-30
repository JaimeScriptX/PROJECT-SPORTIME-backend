<?php

namespace App\Service;

use App\Entity\Events;
use App\Repository\EventsRepository;
use Doctrine\ORM\EntityManagerInterface;

class EventsManager
{
    private $em;
    private $eventsRepository;

    public function __construct(EntityManagerInterface $em, EventsRepository $eventsRepository)
    {
        $this->em = $em;
        $this->eventsRepository = $eventsRepository;
    }

    public function find(int $id) : ?Events
    {
        return $this->eventsRepository->find($id);
    }

    public function getRepository(): EventsRepository
    {
        return $this->eventsRepository; 
    }

    public function create(): Events
    {
        $events = new Events();
        return $events;
    }

    public function save(Events $events): Events
    {
        $this->em->persist($events);
        $this->em->flush();
        return $events;
    }

    public function reload(Events $events): Events
    {
        $this->em->refresh($events);
        return $events;
    }

    public function delete(Events $events): Events
    {
        $this->em->remove($events);
        $this->em->flush();
        return $events;
    } 
}