<?php

namespace App\Entity;

use App\Repository\SportCenterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SportCenterRepository::class)
 */
class SportCenter
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
     * @ORM\Column(type="string", length=255)
     */
    private $municipality;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone;

    /**
     * @ORM\ManyToMany(targetEntity=Sport::class, inversedBy="sportCenters")
     */
    private $fk_sport;

    /**
     * @ORM\OneToMany(targetEntity=LocationSportCenter::class, mappedBy="fk_sport_center")
     */
    private $locationSportCenters;

    /**
     * @ORM\ManyToOne(targetEntity=Services::class, inversedBy="sportCenters")
     */
    private $fk_services;

    /**
     * @ORM\OneToMany(targetEntity=Timetable::class, mappedBy="fk_sportcenter")
     */
    private $timetables;

    /**
     * @ORM\OneToMany(targetEntity=Events::class, mappedBy="fk_sportcenter")
     */
    private $events;

    public function __construct()
    {
        $this->fk_sport = new ArrayCollection();
        $this->locationSportCenters = new ArrayCollection();
        $this->timetables = new ArrayCollection();
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

    public function getMunicipality(): ?string
    {
        return $this->municipality;
    }

    public function setMunicipality(string $municipality): self
    {
        $this->municipality = $municipality;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, Sport>
     */
    public function getFkSport(): Collection
    {
        return $this->fk_sport;
    }

    public function addFkSport(Sport $fkSport): self
    {
        if (!$this->fk_sport->contains($fkSport)) {
            $this->fk_sport[] = $fkSport;
        }

        return $this;
    }

    public function removeFkSport(Sport $fkSport): self
    {
        $this->fk_sport->removeElement($fkSport);

        return $this;
    }

    /**
     * @return Collection<int, LocationSportCenter>
     */
    public function getLocationSportCenters(): Collection
    {
        return $this->locationSportCenters;
    }

    public function addLocationSportCenter(LocationSportCenter $locationSportCenter): self
    {
        if (!$this->locationSportCenters->contains($locationSportCenter)) {
            $this->locationSportCenters[] = $locationSportCenter;
            $locationSportCenter->setFkSportCenter($this);
        }

        return $this;
    }

    public function removeLocationSportCenter(LocationSportCenter $locationSportCenter): self
    {
        if ($this->locationSportCenters->removeElement($locationSportCenter)) {
            // set the owning side to null (unless already changed)
            if ($locationSportCenter->getFkSportCenter() === $this) {
                $locationSportCenter->setFkSportCenter(null);
            }
        }

        return $this;
    }

    public function getFkServices(): ?Services
    {
        return $this->fk_services;
    }

    public function setFkServices(?Services $fk_services): self
    {
        $this->fk_services = $fk_services;

        return $this;
    }

    /**
     * @return Collection<int, Timetable>
     */
    public function getTimetables(): Collection
    {
        return $this->timetables;
    }

    public function addTimetable(Timetable $timetable): self
    {
        if (!$this->timetables->contains($timetable)) {
            $this->timetables[] = $timetable;
            $timetable->setFkSportcenter($this);
        }

        return $this;
    }

    public function removeTimetable(Timetable $timetable): self
    {
        if ($this->timetables->removeElement($timetable)) {
            // set the owning side to null (unless already changed)
            if ($timetable->getFkSportcenter() === $this) {
                $timetable->setFkSportcenter(null);
            }
        }

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
            $event->setFkSportcenter($this);
        }

        return $this;
    }

    public function removeEvent(Events $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getFkSportcenter() === $this) {
                $event->setFkSportcenter(null);
            }
        }

        return $this;
    }
}
