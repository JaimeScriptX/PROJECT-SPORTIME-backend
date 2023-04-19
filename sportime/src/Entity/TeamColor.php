<?php

namespace App\Entity;

use App\Repository\TeamColorRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TeamColorRepository::class)
 */
class TeamColor
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
    private $colours;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getColours(): ?string
    {
        return $this->colours;
    }

    public function setColours(string $colours): self
    {
        $this->colours = $colours;

        return $this;
    }
}
