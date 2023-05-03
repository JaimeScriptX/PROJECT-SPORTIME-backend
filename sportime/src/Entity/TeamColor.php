<?php

namespace App\Entity;

use App\Repository\TeamColorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TeamColorRepository::class)
 */
class TeamColor
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $team_a;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $team_b;

    /**
     * @ORM\OneToMany(targetEntity=Events::class, mappedBy="fk_teamcolor")
     */
    private $events;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeamA(): ?string
    {
        return $this->team_a;
    }

    public function setTeamA(string $team_a): self
    {
        $this->team_a = $team_a;

        return $this;
    }

    public function getTeamB(): ?string
    {
        return $this->team_b;
    }

    public function setTeamB(string $team_b): self
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

    public function addEvent(Events $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setFkTeamcolor($this);
        }

        return $this;
    }

    public function removeEvent(Events $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getFkTeamcolor() === $this) {
                $event->setFkTeamcolor(null);
            }
        }

        return $this;
    }
}
