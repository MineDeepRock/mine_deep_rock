<?php


namespace game_system\service;


use game_system\model\GameId;
use game_system\model\GameScore;
use game_system\model\TeamId;
use game_system\repository\GameScoresRepository;
use Service;

class GameScoresService extends Service
{
    private $repository;

    public function __construct() {
        $this->repository = new GameScoresRepository();
    }

    public function getScores(GameId $gameId): array {
        return $this->repository->getScores($gameId->value());
    }

    public function getTeamScores(TeamId $teamId): array {
        return $this->repository->getTeamScores($teamId->value());
    }

    public function getUserScores(string $name): array {
        return $this->repository->getUserScores($name);
    }

    public function getUserScore(string $name, GameId $gameId): GameScore {
        return $this->repository->getUserScore($name, $gameId->value());
    }

    public function addScore(string $name, GameId $gameId, TeamId $teamId): void {
        $this->repository->addScore($name, $gameId->value(), $teamId->value());
    }

    public function addKillCount(string $name, GameId $gameId): void {
        $this->repository->addKillCount($name, $gameId->value());
    }

    public function addPoint(string $name, GameId $gameId, int $value): void {
        $this->repository->addPoint($name, $gameId->value(), $value);
    }
}
