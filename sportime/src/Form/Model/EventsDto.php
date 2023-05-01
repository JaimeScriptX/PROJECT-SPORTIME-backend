<?php

namespace App\Form\Model;

use App\Entity\Events;

class EventsDto{
    public $name;
    public $is_private;
    public $details;
    public $price;
    public $date;
    public $time;
    public $duration;
    public $number_players;
    public $fk_team_colours;

    public function __constructor()
    {
        $this->fk_team_colours = [];
    }

    public static function createFromEvents(Events $events): self
    {
        $dto = new self();
        $dto->name = $events->getName();
        $dto->is_private = $events->isIsPrivate();
        $dto->details = $events->getDetails();
        $dto->price = $events->getPrice();
        $dto->date = $events->getDate();
        $dto->time = $events->getTime();
        $dto->duration = $events->getDuration();
        $dto->number_players = $events->getNumberPlayers();
        $dto->fk_team_colours = $events->getFkTeamcolor();

        return $dto;
    }
   
}