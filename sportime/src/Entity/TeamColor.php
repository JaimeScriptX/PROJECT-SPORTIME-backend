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
    private $team_a;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $team_b;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeamA(): ?string
    {
        return $this->team_a;
    }

    public function setTeamA(string $team_a): self
    {
        $this->team_a = $team_a;

        return $this;
    }

    public function getTeamB(): ?string
    {
        return $this->team_b;
    }

    public function setTeamB(string $team_b): self
    {
        $this->team_b = $team_b;

        return $this;
    }
}
