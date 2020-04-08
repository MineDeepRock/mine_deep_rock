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

    public function login(string $playerName):Player {
        if ($this->exists($playerName)) {
            return $this->getData($playerName);
        } else {
            return $this->register($playerName);
        }
    }

    private function register(String $playerName):Player {
        $newPlayer = new Player($playerName);
        $this->repository->register($newPlayer);
        return $newPlayer;
    }

    public function exists(string $name):bool {
        return $this->repository->exists($name);
    }


    public function getData(string $name): Player {
        return $this->repository->getData($name);
    }

    public function updateBelongTeamId(string $name, TeamId $teamId): Player {
        $this->repository->updateBelongTeamId($name, $teamId);
        //TODO:変更したプレイヤーデータを返すようにする?
        return $this->getData($name);
    }
}