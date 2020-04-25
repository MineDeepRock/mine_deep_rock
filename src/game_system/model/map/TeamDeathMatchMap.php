<?php


namespace game_system\model\map;

use game_system\model\GameType;

abstract class TeamDeathMatchMap extends Map
{
    private $redTeamSpawnPoints;
    private $blueTeamSpawnPoints;

    public function __construct(string $name, string $creatorName, $redTeamSpawnPoints, $blueTeamSpawnPoints) {
        parent::__construct($name, $creatorName, GameType::TeamDeathMatch());
        $this->redTeamSpawnPoints = $redTeamSpawnPoints;
        $this->blueTeamSpawnPoints = $blueTeamSpawnPoints;
    }

    /**
     * @return mixed
     */
    public function getRedTeamSpawnPoints() {
        return $this->redTeamSpawnPoints;
    }

    /**
     * @return mixed
     */
    public function getBlueTeamSpawnPoints() {
        return $this->blueTeamSpawnPoints;
    }
}