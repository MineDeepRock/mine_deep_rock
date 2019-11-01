<?php

namespace team_system\service;

use team_system\models\Player;
use team_system\models\Team;
use team_system\models\TeamId;
use team_system\repository\TeamRepository;

class TeamService
{

    private $repository;

    public function __construct() {
        $this->repository = new TeamRepository();

    }

    public function contain(TeamId $teamId): bool {
        return $this->repository->contain($teamId);
    }

    public function create(Player $owner) {

        $createdTeam = new Team($owner);

        $this->repository->create($createdTeam);

        return $createdTeam;
    }

    public function breakup() {
        // TODO: Implement breakup() method.
    }
}