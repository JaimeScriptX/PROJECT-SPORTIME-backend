<?php

namespace App\Entity;

use App\Repository\TimetableRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TimetableRepository::class)
 */
class Timetable
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
    private $day;

    /**
     * @ORM\Column(type="time")
     */
    private $open;

    /**
     * @ORM\Column(type="time")
     */
    private $close;

    /**
     * @ORM\ManyToOne(targetEntity=SportCenter::class, inversedBy="timetables")
     */
    private $fk_sportcenter;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getOpen(): ?\DateTimeInterface
    {
        return $this->open;
    }

    public function setOpen(\DateTimeInterface $open): self
    {
        $this->open = $open;

        return $this;
    }

    public function getClose(): ?\DateTimeInterface
    {
        return $this->close;
    }

    public function setClose(\DateTimeInterface $close): self
    {
        $this->close = $close;

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
