<?php

namespace team_system\service;

use team_system\models\Player;
use team_system\models\Team;
use team_system\repository\TeamRepository;

class TeamService implements iTeamService
{

    private $repository;

    public function __construct() {
        $this->repository = new TeamRepository();

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

interface iTeamService
{
    public function create(Player $owner);

    public function breakup();
}