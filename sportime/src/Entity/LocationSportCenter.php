<?php

namespace App\Entity;

use App\Repository\LocationSportCenterRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LocationSportCenterRepository::class)
 */
class LocationSportCenter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $latitude;

    /**
     * @ORM\Column(type="float")
     */
    private $longitude;

    /**
     * @ORM\Column(type="string")
     */
    private $destination;

    /**
     * @ORM\ManyToOne(targetEntity=SportCenter::class, inversedBy="locationSportCenters")
     */
    private $fk_SportCenter;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): self
    {
        $this->destination = $destination;

        return $this;
    }

    public function getFkSportCenter(): ?SportCenter
    {
        return $this->fk_SportCenter;
    }

    public function setFkSportCenter(?SportCenter $fk_SportCenter): self
    {
        $this->fk_SportCenter = $fk_SportCenter;

        return $this;
    }
}
