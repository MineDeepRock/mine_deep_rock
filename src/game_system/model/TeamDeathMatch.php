<?php


namespace game_system\model;


use game_system\model\map\TeamDeathMatchMap;

date_default_timezone_set('Asia/Tokyo');

class TeamDeathMatch extends Game
{
    private $elapsedSecond;

    private $redTeam;
    public $redTeamScore;
    private $redTeamSpawnPoints;

    private $blueTeam;
    public $blueTeamScore;
    private $blueTeamSpawnPoints;

    private $map;

    public function __construct(TeamDeathMatchMap $map) {
        $this->elapsedSecond = 0;


        $this->redTeam = new Team();
        $this->redTeamScore = 0;
        $this->redTeamSpawnPoints = $map->getRedTeamSpawnPoints();

        $this->blueTeam = new Team();
        $this->blueTeamScore = 0;
        $this->blueTeamSpawnPoints = $map->getBlueTeamSpawnPoints();

        $this->map = $map;
        parent::__construct();
    }

    public function start(): void {
        $this->isStarted = true;
    }


    /**
     * @return Team
     */
    public function getRedTeam(): Team {
        return $this->redTeam;
    }

    /**
     * @return Team
     */
    public function getBlueTeam(): Team {
        return $this->blueTeam;
    }

    /**
     * @return TeamDeathMatchMap
     */
    public function getMap(): TeamDeathMatchMap {
        return $this->map;
    }

    /**
     * @param TeamId $userTeamId
     * @return Coordinate
     */
    public function getSpawnPoint(TeamId $userTeamId): Coordinate {
        if ($userTeamId->equal($this->redTeam->getId())) {
            return $this->redTeamSpawnPoints[rand(0, count($this->redTeamSpawnPoints) - 1)];
        } else {
            return $this->blueTeamSpawnPoints[rand(0, count($this->blueTeamSpawnPoints) - 1)];
        }
    }

    /**
     * @return int
     */
    public function getElapsedSecond(): int {
        return $this->elapsedSecond;
    }

    public function pass(): void {
        $this->elapsedSecond++;
    }

    public function getWinTeam(): Team {
        return $this->redTeamScore > $this->blueTeamScore ? $this->redTeam : $this->blueTeam;
    }
}