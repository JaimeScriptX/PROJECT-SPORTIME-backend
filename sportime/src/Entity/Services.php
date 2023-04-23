<?php

namespace App\Entity;

use App\Repository\ServicesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServicesRepository::class)
 */
class Services
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
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=SportCenter::class, mappedBy="fk_services")
     */
    private $sportCenters;

    public function __construct()
    {
        $this->sportCenters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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
            $sportCenter->setFkServices($this);
        }

        return $this;
    }

    public function removeSportCenter(SportCenter $sportCenter): self
    {
        if ($this->sportCenters->removeElement($sportCenter)) {
            // set the owning side to null (unless already changed)
            if ($sportCenter->getFkServices() === $this) {
                $sportCenter->setFkServices(null);
            }
        }

        return $this;
    }
}
