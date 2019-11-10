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

    private function contain(Player $owner): bool {
        return $this->repository->contain($owner);
    }

    public function create(Player $owner) {

        if ($this->contain($owner)) {
            return false;
        }

        $createdTeam = new Team($owner);

        $this->repository->create($createdTeam);

        return true;
    }

    public function breakup() {
        // TODO: Implement breakup() method.
    }
}