<?php

namespace App\Entity;

use App\Repository\EventsResultsRepository;
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
     * @ORM\OneToOne(targetEntity=Events::class, inversedBy="eventsResults", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $fk_events;

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

    public function getFkEvents(): ?Events
    {
        return $this->fk_events;
    }

    public function setFkEvents(Events $fk_events): self
    {
        $this->fk_events = $fk_events;

        return $this;
    }
}
