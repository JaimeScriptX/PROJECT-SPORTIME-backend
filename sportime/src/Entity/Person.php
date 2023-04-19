<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PersonRepository::class)
 */
class Person
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $image_profile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $last_name;

    /**
     * @ORM\Column(type="date")
     */
    private $birthday;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $weight;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $height;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nationality;

    /**
     * @ORM\ManyToOne(targetEntity=Events::class, inversedBy="fk_person")
     * @ORM\JoinColumn(nullable=false)
     */
    private $events;

    /**
     * @ORM\ManyToOne(targetEntity=Sex::class, inversedBy="people")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fk_sex;

    /**
     * @ORM\OneToMany(targetEntity=EventPlayers::class, mappedBy="fk_person")
     */
    private $eventPlayers;

    public function __construct()
    {
        $this->eventPlayers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageProfile(): ?string
    {
        return $this->image_profile;
    }

    public function setImageProfile(?string $image_profile): self
    {
        $this->image_profile = $image_profile;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(?string $nationality): self
    {
        $this->nationality = $nationality;

        return $this;
    }

    public function getEvents(): ?Events
    {
        return $this->events;
    }

    public function setEvents(?Events $events): self
    {
        $this->events = $events;

        return $this;
    }

    public function getFkSex(): ?Sex
    {
        return $this->fk_sex;
    }

    public function setFkSex(?Sex $fk_sex): self
    {
        $this->fk_sex = $fk_sex;

        return $this;
    }

    /**
     * @return Collection<int, EventPlayers>
     */
    public function getEventPlayers(): Collection
    {
        return $this->eventPlayers;
    }

    public function addEventPlayer(EventPlayers $eventPlayer): self
    {
        if (!$this->eventPlayers->contains($eventPlayer)) {
            $this->eventPlayers[] = $eventPlayer;
            $eventPlayer->setFkPerson($this);
        }

        return $this;
    }

    public function removeEventPlayer(EventPlayers $eventPlayer): self
    {
        if ($this->eventPlayers->removeElement($eventPlayer)) {
            // set the owning side to null (unless already changed)
            if ($eventPlayer->getFkPerson() === $this) {
                $eventPlayer->setFkPerson(null);
            }
        }

        return $this;
    }
}
