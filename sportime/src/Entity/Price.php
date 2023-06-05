<?php

namespace App\Entity;

use App\Repository\PriceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PriceRepository::class)
 */
class Price
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
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity=Sport::class, inversedBy="prices")
     */
    private $fk_sport;

    /**
     * @ORM\ManyToOne(targetEntity=SportCenter::class, inversedBy="prices")
     */
    private $fk_sportcenter;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

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
}
