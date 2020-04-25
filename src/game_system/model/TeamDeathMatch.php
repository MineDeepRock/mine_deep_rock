<?php


namespace game_system\model;


class TeamDeathMatch extends Game
{
    private $redTeam;
    private $redTeamScore;
    private $redTeamSpawnPoints;

    private $blueTeam;
    private $blueTeamScore;
    private $blueTeamSpawnPoints;

    public function __construct() {
        $this->redTeam = new Team();
        $this->redTeamScore = 0;

        $this->blueTeam = new Team();
        $this->blueTeamScore = 0;
    }

    private function initSpawnPoint() {
        //TODO:実装
    }

    public function spawn(User $user): void {

    }
}