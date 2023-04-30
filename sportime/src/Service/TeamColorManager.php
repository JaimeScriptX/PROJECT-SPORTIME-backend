<?php

namespace App\Service;

use App\Entity\TeamColor;
use App\Repository\EventsRepository;
use App\Repository\TeamColorRepository;
use Doctrine\ORM\EntityManagerInterface;

class TeamColorManager
{
    private $em;
    private $teamColorRepository;

    public function __construct(EntityManagerInterface $em, TeamColorRepository $teamColorRepository)
    {
        $this->em = $em;
        $this->teamColorRepository = $teamColorRepository;
    }

    public function find(int $id) : ?TeamColor
    {
        return $this->teamColorRepository->find($id);
    }

    public function getRepository(): TeamColorRepository
    {
        return $this->teamColorRepository; 
    }

    public function create(): TeamColor
    {
        $teamColor = new TeamColor();
        return $teamColor;
    }

    public function persist(TeamColor $teamColor): TeamColor
    {
        $this->em->persist($teamColor);
        return $teamColor;
    }

    public function save(TeamColor $teamColor): TeamColor
    {
        $this->em->persist($teamColor);
        $this->em->flush();
        return $teamColor;
    }

    public function reload(TeamColor $teamColor): TeamColor
    {
        $this->em->refresh($teamColor);
        return $teamColor;
    }
}