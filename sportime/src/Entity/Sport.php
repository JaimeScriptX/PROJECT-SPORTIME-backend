<?php

namespace App\Entity;

use App\Repository\SportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SportRepository::class)
 */
class Sport
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
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToMany(targetEntity=SportCenter::class, mappedBy="fk_sport")
     */
    private $sportCenters;

    /**
     * @ORM\OneToMany(targetEntity=Events::class, mappedBy="fk_sport")
     */
    private $events;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $logo_event;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $logo_sportcenter;

    public function __construct()
    {
        $this->sportCenters = new ArrayCollection();
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, SportCenter>
     */
    public function getSportCenters(): Collection
    {
        return $this->sportCenters;
    }

    public function addSportCenter(SportCenter $sportCenter): self
    {
        if (!$this->sportCenters->contains($sportCenter)) {
            $this->sportCenters[] = $sportCenter;
            $sportCenter->addFkSport($this);
        }

        return $this;
    }

    public function removeSportCenter(SportCenter $sportCenter): self
    {
        if ($this->sportCenters->removeElement($sportCenter)) {
            $sportCenter->removeFkSport($this);
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
            $event->setFkSport($this);
        }

        return $this;
    }

    public function removeEvent(Events $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getFkSport() === $this) {
                $event->setFkSport(null);
            }
        }

        return $this;
    }

    public function getLogoEvent(): ?string
    {
        return $this->logo_event;
    }

    public function setLogoEvent(?string $logo_event): self
    {
        $this->logo_event = $logo_event;

        return $this;
    }

    public function getLogoSportcenter(): ?string
    {
        return $this->logo_sportcenter;
    }

    public function setLogoSportcenter(?string $logo_sportcenter): self
    {
        $this->logo_sportcenter = $logo_sportcenter;

        return $this;
    }
}
