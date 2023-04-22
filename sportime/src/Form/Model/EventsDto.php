<?php

namespace App\Form\Model;

use App\Entity\Events;

class EventsDto{
    public $name;

    public function __constructor()
    {

    }

    public static function createFormEvents(Events $events): self
    {
        $dto = new self();
        $dto->name = $events->getName();
        return $dto;
    }
}