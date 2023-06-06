<?php

namespace App\Entity;

use App\Repository\EventsResultsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EventsResultsRepository::class)
 */
class EventsResults
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $team_a;

    /**
     * @ORM\Column(type="integer")
     */
    private $team_b;

    /**
     * @ORM\OneToMany(targetEntity=Events::class, mappedBy="fk_results")
     */
    private $events;

    /**
     * @ORM\ManyToOne(targetEntity=Events::class, inversedBy="eventsResults")
     */
    private $fk_event;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeamA(): ?int
    {
        return $this->team_a;
    }

    public function setTeamA(int $team_a): self
    {
        $this->team_a = $team_a;

        return $this;
    }

    public function getTeamB(): ?int
    {
        return $this->team_b;
    }

    public function setTeamB(int $team_b): self
    {
        $this->team_b = $team_b;

        return $this;
    }

    /**
     * @return Collection<int, Events>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function getFkEvent(): ?Events
    {
        return $this->fk_event;
    }

    public function setFkEvent(?Events $fk_event): self
    {
        $this->fk_event = $fk_event;

        return $this;
    }


}
