<?php


namespace game_system\model\map;


use game_system\model\GameType;

class TeamDominationMap extends Map
{
    private $redTeamSpawnPoints;
    private $blueTeamSpawnPoints;

    private $pointACoordinate;
    private $pointBCoordinate;
    private $pointCCoordinate;

    public function __construct(string $name, string $creatorName, $redTeamSpawnPoints, $blueTeamSpawnPoints) {
        parent::__construct($name, $creatorName, GameType::TeamDomination());
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