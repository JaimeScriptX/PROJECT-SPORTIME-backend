<?php

namespace App\Entity;

use App\Repository\EventsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EventsRepository::class)
 */
class Events
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
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_private;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $details;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="time")
     */
    private $time;

    /**
     * @ORM\Column(type="time")
     */
    private $duration;

    /**
     * @ORM\Column(type="integer")
     */
    private $number_players;

    /**
     * @ORM\OneToMany(targetEntity=Person::class, mappedBy="events")
     */
    private $fk_person;

    /**
     * @ORM\ManyToOne(targetEntity=Sport::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fk_sport;

    /**
     * @ORM\ManyToOne(targetEntity=SportCenter::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fk_sport_center;

    public function __construct()
    {
        $this->fk_person = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function isIsPrivate(): ?bool
    {
        return $this->is_private;
    }

    public function setIsPrivate(bool $is_private): self
    {
        $this->is_private = $is_private;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getDuration(): ?\DateTimeInterface
    {
        return $this->duration;
    }

    public function setDuration(\DateTimeInterface $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getNumberPlayers(): ?int
    {
        return $this->number_players;
    }

    public function setNumberPlayers(int $number_players): self
    {
        $this->number_players = $number_players;

        return $this;
    }

    /**
     * @return Collection<int, Person>
     */
    public function getFkPerson(): Collection
    {
        return $this->fk_person;
    }

    public function addFkPerson(Person $fkPerson): self
    {
        if (!$this->fk_person->contains($fkPerson)) {
            $this->fk_person[] = $fkPerson;
            $fkPerson->setEvents($this);
        }

        return $this;
    }

    public function removeFkPerson(Person $fkPerson): self
    {
        if ($this->fk_person->removeElement($fkPerson)) {
            // set the owning side to null (unless already changed)
            if ($fkPerson->getEvents() === $this) {
                $fkPerson->setEvents(null);
            }
        }

        return $this;
    }

    public function getFkSport(): ?Sport
    {
        return $this->fk_sport;
    }

    public function setFkSport(?Sport $fk_sport): self
    {
        $this->fk_sport = $fk_sport;

        return $this;
    }

    public function getFkSportCenter(): ?SportCenter
    {
        return $this->fk_sport_center;
    }

    public function setFkSportCenter(?SportCenter $fk_sport_center): self
    {
        $this->fk_sport_center = $fk_sport_center;

        return $this;
    }
}
