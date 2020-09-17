<?php


namespace mine_deep_rock\model;


use pocketmine\level\Position;
use team_game_system\model\GameId;
use team_game_system\model\TeamId;

class Core
{
    /**
     * @var GameId
     */
    private $gameId;
    /**
     * @var TeamId
     */
    private $teamId;
    /**
     * @var int
     */
    private $health;
    /**
     * @var Position
     */
    private $position;

    public function __construct(GameId $gameId, TeamId $teamId, Position $position, int $health = 100) {
        $this->gameId = $gameId;
        $this->teamId = $teamId;
        $this->position = $position;
        $this->health = $health;
    }

    /**
     * @return GameId
     */
    public function getGameId(): GameId {
        return $this->gameId;
    }

    /**
     * @return TeamId
     */
    public function getTeamId(): TeamId {
        return $this->teamId;
    }

    /**
     * @return int
     */
    public function getHealth(): int {
        return $this->health;
    }

    /**
     * @param int $health
     */
    public function setHealth(int $health): void {
        $this->health = $health;
    }

    /**
     * @return Position
     */
    public function getPosition(): Position {
        return $this->position;
    }
}