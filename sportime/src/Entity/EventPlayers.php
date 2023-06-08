<?php

namespace App\Entity;

use App\Repository\EventPlayersRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity(repositoryClass=EventPlayersRepository::class)
 */
class EventPlayers
{
    /**
    * @var \Ramsey\Uuid\UuidInterface
    *
    * @ORM\Id
    * @ORM\Column(type="uuid", unique=true)
    * @ORM\GeneratedValue(strategy="CUSTOM")
    * @ORM\CustomIdGenerator(class=UuidGenerator::class)
    */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Events::class, inversedBy="eventPlayers")
     */
    private $fk_event;

    /**
     * @ORM\ManyToOne(targetEntity=Person::class, inversedBy="eventPlayers")
     */
    private $fk_person;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $team;

    public function getId()
    {
        return $this->id;
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

    public function getFkPerson(): ?Person
    {
        return $this->fk_person;
    }

    public function setFkPerson(?Person $fk_person): self
    {
        $this->fk_person = $fk_person;

        return $this;
    }

    public function getEquipo(): ?int
    {
        return $this->team;
    }

    public function setEquipo(?int $team): self
    {
        $this->team = $team;

        return $this;
    }
}
