<?php

namespace App\Entity;

use App\Repository\ServicesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity(repositoryClass=ServicesRepository::class)
 */
class Services
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
     * @ORM\ManyToMany(targetEntity=SportCenter::class, mappedBy="fk_services")
     */
    private $sportCenters;

    public function __construct()
    {
        $this->sportCenters = new ArrayCollection();
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
            $sportCenter->addFkService($this);
        }

        return $this;
    }

    public function removeSportCenter(SportCenter $sportCenter): self
    {
        if ($this->sportCenters->removeElement($sportCenter)) {
            $sportCenter->removeFkService($this);
        }

        return $this;
    }
}
