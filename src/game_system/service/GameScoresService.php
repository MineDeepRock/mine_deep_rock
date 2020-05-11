<?php


namespace game_system\service;


use game_system\model\GameId;
use game_system\model\GameScore;
use game_system\repository\GameScoresRepository;
use Service;

class GameScoresService extends Service
{
    private $repository;

    public function __construct() {
        $this->repository = new GameScoresRepository();
    }

    public function getScores(string $name): array {
        return $this->repository->getScores($name);
    }

    public function getScore(string $name, GameId $gameId): GameScore {
        return $this->repository->getScore($name, $gameId->value());
    }

    public function addScore(string $name, GameId $gameId): void {
        $this->repository->addScore($name, $gameId->value());
    }

    public function addKillCount(string $name, GameId $gameId): void {
        $this->repository->addKillCount($name, $gameId->value());
    }

    public function addPoint(string $name, GameId $gameId, int $value): void {
        $this->repository->addPoint($name, $gameId->value(),$value);
    }
}
