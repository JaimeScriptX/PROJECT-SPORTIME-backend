<?php

namespace App\Entity;

use App\Repository\ScheduleCenterRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity(repositoryClass=ScheduleCenterRepository::class)
 */
class ScheduleCenter
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
     * @ORM\Column(type="integer")
     */
    private $day;

    /**
     * @ORM\Column(type="time")
     */
    private $start;

    /**
     * @ORM\Column(type="time")
     */
    private $end;

    /**
     * @ORM\ManyToOne(targetEntity=SportCenter::class, inversedBy="scheduleCenters")
     */
    private $fk_sport_center_id;

    public function getId()
    {
        return $this->id;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(int $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getFkSportCenterId(): ?SportCenter
    {
        return $this->fk_sport_center_id;
    }

    public function setFkSportCenterId(?SportCenter $fk_sport_center_id): self
    {
        $this->fk_sport_center_id = $fk_sport_center_id;

        return $this;
    }
}
