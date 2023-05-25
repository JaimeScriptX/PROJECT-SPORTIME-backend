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
    private $colour;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image_shirt;

    /**
     * @ORM\OneToMany(targetEntity=Events::class, mappedBy="fk_teamcolor")
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity=Events::class, mappedBy="fk_teamcolor_two")
     */
    private $events_two;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->events_two = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getColour(): ?string
    {
        return $this->colour;
    }

    public function setTeamA(string $colour): self
    {
        $this->colour = $colour;

        return $this;
    }

    public function getTeamB(): ?string
    {
        return $this->image_shirt;
    }

    public function setTeamB(string $image_shirt): self
    {
        $this->image_shirt = $image_shirt;

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

    /**
     * @return Collection<int, Events>
     */
    public function getEventsTwo(): Collection
    {
        return $this->events_two;
    }

    public function addEventsTwo(Events $eventsTwo): self
    {
        if (!$this->events_two->contains($eventsTwo)) {
            $this->events_two[] = $eventsTwo;
            $eventsTwo->setFkTeamcolorTwo($this);
        }

        return $this;
    }

    public function removeEventsTwo(Events $eventsTwo): self
    {
        if ($this->events_two->removeElement($eventsTwo)) {
            // set the owning side to null (unless already changed)
            if ($eventsTwo->getFkTeamcolorTwo() === $this) {
                $eventsTwo->setFkTeamcolorTwo(null);
            }
        }

        return $this;
    }
}
