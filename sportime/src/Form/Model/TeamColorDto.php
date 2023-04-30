<?php

namespace App\Form\Model;

use App\Entity\TeamColor;

class TeamColorDto {
    public $id;
    public $team_a;
    public $team_b;

    public static function createFromTeamColor(TeamColor $teamColor): self {
        $dto = new self();
        $dto->id = $teamColor->getId();
        $dto->team_a = $teamColor->getTeamA();
        $dto->team_b = $teamColor->getTeamB();

        return $dto;
    }
}