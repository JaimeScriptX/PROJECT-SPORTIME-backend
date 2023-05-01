<?php

namespace App\Service;

use App\Entity\Events;
use App\Form\Model\EventsDto;
use App\Form\Type\EventsFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\EventsManager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class EventsFormProcessor
{
    private $eventsManager;
    private $formFactory;

    public function __construct(
        EventsManager $eventsManager,
        FormFactoryInterface $formFactory
    ){
        $this->eventsManager = $eventsManager;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Events $events, Request $request): array
    {
        $eventsDto = EventsDto::createFromEvents($events);

       

        $form = $this->formFactory->create(EventsFormType::class, $eventsDto);
        $form->handleRequest($request);
        if (!$form->isSubmitted()){
            return [null, "Form is not submitted"];
        }
        if ($form->isValid()){
           

            // set
            $events->setName($eventsDto->name);
            $events->setIsPrivate($eventsDto->is_private);
            $events->setDetails($eventsDto->details);
            $events->setPrice($eventsDto->price);
            $events->setDate($eventsDto->date);
            $events->setTime($eventsDto->time);
            $events->setDuration($eventsDto->duration);
            $events->setNumberPlayers($eventsDto->number_players);


            // guardar
            $this->eventsManager->save($events);
            $this->eventsManager->reload($events);
            return [$events, null];
        }
        return [null, $form];
    }
}

/*
 //TeamColor
        $originalTeamColor = new ArrayCollection();
        foreach ($events->getFkTeamColours() as $teamColor) {
            $teamColorDto = TeamColorDto::createFromTeamColor($teamColor);
            $eventsDto->fk_team_colours[] = $teamColorDto;
            $originalTeamColor->add($teamColorDto);
        }


 //remove teamColor
            foreach ($originalTeamColor as $originalTeamColorDto){
                if(!in_array($originalTeamColorDto, $eventsDto->fk_team_colours)){
                    $teamColor = $this->teamColorManager->find($originalTeamColorDto->id);
                    $events->removeFkTeamColour($teamColor);
                }
            }

//add teamColor
            foreach ($eventsDto->fk_team_colours as $newTeamColorDto){
                if(!$originalTeamColor->contains($newTeamColorDto)){
                    $teamColor = $this->teamColorManager->find($newTeamColorDto->id ?? 0);
                    if (!$teamColor){
                        $teamColor = $this->teamColorManager->create();
                        $teamColor->setTeamA($newTeamColorDto->teamA);
                        $teamColor->setTeamB($newTeamColorDto->teamB);
                        $this->teamColorManager->persist($teamColor);
                    }
                    $events->setFkTeamColours($teamColor);
                }
            }
*/