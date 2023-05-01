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
     * @ORM\OneToMany(targetEntity=EventsResults::class, mappedBy="fk_events")
     */
    private $eventsResults;

    /**
     * @ORM\ManyToOne(targetEntity=Sport::class, inversedBy="events")
     */
    private $fk_sport;

    /**
     * @ORM\ManyToOne(targetEntity=SportCenter::class, inversedBy="events")
     */
    private $fk_sportcenter;

    /**
     * @ORM\ManyToOne(targetEntity=Difficulty::class, inversedBy="events")
     */
    private $fk_difficulty;

    /**
     * @ORM\ManyToOne(targetEntity=Sex::class, inversedBy="events")
     */
    private $fk_sex;

    /**
     * @ORM\ManyToOne(targetEntity=Person::class, inversedBy="events")
     */
    private $fk_person;

    /**
     * @ORM\OneToMany(targetEntity=EventPlayers::class, mappedBy="fk_event")
     */
    private $eventPlayers;

    /**
     * @ORM\OneToMany(targetEntity=Events::class, mappedBy="fk_team_colours")
     */
    private $events;

    /**
     * @ORM\OneToOne(targetEntity=TeamColor::class, cascade={"persist", "remove"})
     */
    private $fk_teamcolor;


    public function __construct()
    {
        $this->eventsResults = new ArrayCollection();
        $this->eventPlayers = new ArrayCollection();
        $this->events = new ArrayCollection();
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
     * @return Collection<int, EventsResults>
     */
    public function getEventsResults(): Collection
    {
        return $this->eventsResults;
    }

    public function addEventsResult(EventsResults $eventsResult): self
    {
        if (!$this->eventsResults->contains($eventsResult)) {
            $this->eventsResults[] = $eventsResult;
            $eventsResult->setFkEvents($this);
        }

        return $this;
    }

    public function removeEventsResult(EventsResults $eventsResult): self
    {
        if ($this->eventsResults->removeElement($eventsResult)) {
            // set the owning side to null (unless already changed)
            if ($eventsResult->getFkEvents() === $this) {
                $eventsResult->setFkEvents(null);
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

    public function getFkSportcenter(): ?SportCenter
    {
        return $this->fk_sportcenter;
    }

    public function setFkSportcenter(?SportCenter $fk_sportcenter): self
    {
        $this->fk_sportcenter = $fk_sportcenter;

        return $this;
    }

    public function getFkDifficulty(): ?Difficulty
    {
        return $this->fk_difficulty;
    }

    public function setFkDifficulty(?Difficulty $fk_difficulty): self
    {
        $this->fk_difficulty = $fk_difficulty;

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

    public function getFkPerson(): ?Person
    {
        return $this->fk_person;
    }

    public function setFkPerson(?Person $fk_person): self
    {
        $this->fk_person = $fk_person;

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
            $eventPlayer->setFkEvent($this);
        }

        return $this;
    }

    public function removeEventPlayer(EventPlayers $eventPlayer): self
    {
        if ($this->eventPlayers->removeElement($eventPlayer)) {
            // set the owning side to null (unless already changed)
            if ($eventPlayer->getFkEvent() === $this) {
                $eventPlayer->setFkEvent(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, self>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function getFkTeamcolor(): ?TeamColor
    {
        return $this->fk_teamcolor;
    }

    public function setFkTeamcolor(?TeamColor $fk_teamcolor): self
    {
        $this->fk_teamcolor = $fk_teamcolor;

        return $this;
    }



}
