<?php

namespace App\Entity;

use App\Repository\SportCenterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity(repositoryClass=SportCenterRepository::class)
 */
class SportCenter
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
     * @ORM\OneToMany(targetEntity=Events::class, mappedBy="fk_sportcenter")
     */
    private $events;

    /**
     * @ORM\ManyToMany(targetEntity=Services::class, inversedBy="sportCenters")
     */
    private $fk_services;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $image_gallery1;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $image_gallery2;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $image_gallery3;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $image_gallery4;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $destination;

    /**
     * @ORM\Column(type="string", length=512)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=ScheduleCenter::class, mappedBy="fk_sport_center_id")
     */
    private $scheduleCenters;

    /**
     * @ORM\OneToMany(targetEntity=ReservedTime::class, mappedBy="fk_sport_center_id")
     */
    private $reservedTimes;

    /**
     * @ORM\Column(type="float")
     */
    private $price;


    public function __construct()
    {
        $this->fk_sport = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->fk_services = new ArrayCollection();
        $this->scheduleCenters = new ArrayCollection();
        $this->reservedTimes = new ArrayCollection();

    }

    public function getId()
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

    /**
     * @return Collection<int, Services>
     */
    public function getFkServices(): Collection
    {
        return $this->fk_services;
    }

    public function addFkService(Services $fkService): self
    {
        if (!$this->fk_services->contains($fkService)) {
            $this->fk_services[] = $fkService;
        }

        return $this;
    }

    public function removeFkService(Services $fkService): self
    {
        $this->fk_services->removeElement($fkService);

        return $this;
    }

    public function getImageGallery1(): ?string
    {
        return $this->image_gallery1;
    }

    public function setImageGallery1(?string $image_gallery1): self
    {
        $this->image_gallery1 = $image_gallery1;

        return $this;
    }

    public function getImageGallery2(): ?string
    {
        return $this->image_gallery2;
    }

    public function setImageGallery2(?string $image_gallery2): self
    {
        $this->image_gallery2 = $image_gallery2;

        return $this;
    }

    public function getImageGallery3(): ?string
    {
        return $this->image_gallery3;
    }

    public function setImageGallery3(?string $image_gallery3): self
    {
        $this->image_gallery3 = $image_gallery3;

        return $this;
    }

    public function getImageGallery4(): ?string
    {
        return $this->image_gallery4;
    }

    public function setImageGallery4(?string $image_gallery4): self
    {
        $this->image_gallery4 = $image_gallery4;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(?string $destination): self
    {
        $this->destination = $destination;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, ScheduleCenter>
     */
    public function getScheduleCenters(): Collection
    {
        return $this->scheduleCenters;
    }

    public function addScheduleCenter(ScheduleCenter $scheduleCenter): self
    {
        if (!$this->scheduleCenters->contains($scheduleCenter)) {
            $this->scheduleCenters[] = $scheduleCenter;
            $scheduleCenter->setFkSportCenterId($this);
        }

        return $this;
    }

    public function removeScheduleCenter(ScheduleCenter $scheduleCenter): self
    {
        if ($this->scheduleCenters->removeElement($scheduleCenter)) {
            // set the owning side to null (unless already changed)
            if ($scheduleCenter->getFkSportCenterId() === $this) {
                $scheduleCenter->setFkSportCenterId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ReservedTime>
     */
    public function getReservedTimes(): Collection
    {
        return $this->reservedTimes;
    }

    public function addReservedTime(ReservedTime $reservedTime): self
    {
        if (!$this->reservedTimes->contains($reservedTime)) {
            $this->reservedTimes[] = $reservedTime;
            $reservedTime->setFkSportCenterId($this);
        }

        return $this;
    }

    public function removeReservedTime(ReservedTime $reservedTime): self
    {
        if ($this->reservedTimes->removeElement($reservedTime)) {
            // set the owning side to null (unless already changed)
            if ($reservedTime->getFkSportCenterId() === $this) {
                $reservedTime->setFkSportCenterId(null);
            }
        }

        return $this;
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


}
