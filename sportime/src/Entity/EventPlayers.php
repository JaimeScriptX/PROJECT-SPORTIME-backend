<?php

namespace App\Entity;

use App\Repository\EventPlayersRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EventPlayersRepository::class)
 */
class EventPlayers
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Person::class, inversedBy="eventPlayers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fk_person;

    /**
     * @ORM\ManyToOne(targetEntity=Events::class, inversedBy="eventPlayers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fk_event;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFkPerson(): ?Person
    {
        return $this->fk_person;
    }

    public function setFkPerson(?Person $fk_person): self
    {
        $this->fk_person = $fk_person;

        return $this;
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
