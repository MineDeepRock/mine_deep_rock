<?php


namespace game_system\model;


abstract class Map
{
    private $name;
    private $creatorName;
    private $supportGameType;

    public function __construct(string $name, string $creatorName, GameType $supportGameType) {

        $this->name = $name;
        $this->creatorName = $creatorName;
        $this->supportGameType = $supportGameType;
    }
}

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