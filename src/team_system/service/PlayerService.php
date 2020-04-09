<?php


namespace team_system\service;


use team_system\models\Player;
use team_system\models\TeamId;
use team_system\repository\PlayerRepository;

class PlayerService
{
    private $repository;

    public function __construct() {
        $this->repository = new PlayerRepository();
    }

    public function init(string $playerName):Player {
        $newPlayer = new Player($playerName);
        $this->repository->init($newPlayer);
        return $newPlayer;
    }

    public function getData(string $name): Player {
        return $this->repository->getData($name);
    }

    /**
     * @param string $name
     * @param TeamId|null $teamId
     * @return Player
     */
    public function updateBelongTeamId(string $name, ?TeamId $teamId): Player {
        $this->repository->updateBelongTeamId($name, $teamId);
        return $this->getData($name);
    }
}