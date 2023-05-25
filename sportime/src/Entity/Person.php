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
     * @ORM\Column(type="date", nullable=true)
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
     * @ORM\ManyToOne(targetEntity=Sex::class, inversedBy="people")
     */
    private $fk_sex;

    /**
     * @ORM\OneToMany(targetEntity=Events::class, mappedBy="fk_person")
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity=EventPlayers::class, mappedBy="fk_person")
     */
    private $eventPlayers;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="person")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fk_user;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
 
    private $games_played = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */

    private $victories = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $defeat = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $image_banner;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name_and_lastname;

   

    public function __construct()
    {
        $this->events = new ArrayCollection();
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
            $event->setFkPerson($this);
        }

        return $this;
    }

    public function removeEvent(Events $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getFkPerson() === $this) {
                $event->setFkPerson(null);
            }
        }

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

    public function getFkUser(): ?User
    {
        return $this->fk_user;
    }

    public function setFkUser(?User $fk_user): self
    {
        $this->fk_user = $fk_user;

        return $this;
    }

    public function getGamesPlayed(): ?int
    {
        return $this->games_played;
    }

    public function setGamesPlayed(int $games_played): self
    {
        $this->games_played = $games_played;

        return $this;
    }

    public function getVictories(): ?int
    {
        return $this->victories;
    }

    public function setVictories(int $victories): self
    {
        $this->victories = $victories;

        return $this;
    }

    public function getDefeat(): ?int
    {
        return $this->defeat;
    }

    public function setDefeat(int $defeat): self
    {
        $this->defeat = $defeat;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getImageBanner(): ?string
    {
        return $this->image_banner;
    }

    public function setImageBanner(?string $image_banner): self
    {
        $this->image_banner = $image_banner;

        return $this;
    }

    public function getNameAndLastname(): ?string
    {
        return $this->name_and_lastname;
    }

    public function setNameAndLastname(string $name_and_lastname): self
    {
        $this->name_and_lastname = $name_and_lastname;

        return $this;
    }

    
}
