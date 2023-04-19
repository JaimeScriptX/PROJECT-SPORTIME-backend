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
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $need_team;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToMany(targetEntity=SportCenter::class, mappedBy="fk_sport")
     */
    private $fk_sportcenter;

    /**
     * @ORM\OneToMany(targetEntity=Events::class, mappedBy="fk_sport")
     */
    private $events;

    public function __construct()
    {
        $this->fk_sportcenter = new ArrayCollection();
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

    public function isNeedTeam(): ?bool
    {
        return $this->need_team;
    }

    public function setNeedTeam(?bool $need_team): self
    {
        $this->need_team = $need_team;

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
    public function getFkSportcenter(): Collection
    {
        return $this->fk_sportcenter;
    }

    public function addFkSportcenter(SportCenter $fkSportcenter): self
    {
        if (!$this->fk_sportcenter->contains($fkSportcenter)) {
            $this->fk_sportcenter[] = $fkSportcenter;
            $fkSportcenter->addFkSport($this);
        }

        return $this;
    }

    public function removeFkSportcenter(SportCenter $fkSportcenter): self
    {
        if ($this->fk_sportcenter->removeElement($fkSportcenter)) {
            $fkSportcenter->removeFkSport($this);
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
}
