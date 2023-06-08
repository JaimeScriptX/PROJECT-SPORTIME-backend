<?php

namespace App\Entity;

use App\Repository\ReservedTimeRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity(repositoryClass=ReservedTimeRepository::class)
 */
class ReservedTime
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
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="time")
     */
    private $start;

    /**
     * @ORM\Column(type="time")
     */
    private $end;

    /**
     * @ORM\Column(type="date")
     */
    private $date_created;

    /**
     * @ORM\Column(type="boolean")
     */
    private $canceled;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cancellation_reason;

    /**
     * @ORM\ManyToOne(targetEntity=SportCenter::class, inversedBy="reservedTimes")
     */
    private $fk_sport_center_id;

    /**
     * @ORM\ManyToOne(targetEntity=Events::class, inversedBy="reservedTimes")
     */
    private $fk_event_id;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->date_created;
    }

    public function setDateCreated(\DateTimeInterface $date_created): self
    {
        $this->date_created = $date_created;

        return $this;
    }

    public function isCanceled(): ?bool
    {
        return $this->canceled;
    }

    public function setCanceled(bool $canceled): self
    {
        $this->canceled = $canceled;

        return $this;
    }

    public function getCancellationReason(): ?string
    {
        return $this->cancellation_reason;
    }

    public function setCancellationReason(?string $cancellation_reason): self
    {
        $this->cancellation_reason = $cancellation_reason;

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

    public function getFkEventId(): ?Events
    {
        return $this->fk_event_id;
    }

    public function setFkEventId(?Events $fk_event_id): self
    {
        $this->fk_event_id = $fk_event_id;

        return $this;
    }
}
