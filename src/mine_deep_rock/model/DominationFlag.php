<?php


namespace mine_deep_rock\model;


use pocketmine\level\Position;
use team_game_system\model\GameId;
use team_game_system\model\TeamId;

class DominationFlag
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var GameId
     */
    private $gameId;

    /**
     * @var Position
     */
    private $position;

    /**
     * @var DominationFlagGauge
     */
    private $gauge;

    private $range;

    public function __construct(string $name, GameId $gameId, Position $position, DominationFlagGauge $gauge, int $range) {
        $this->name = $name;
        $this->gameId = $gameId;
        $this->position = $position;
        $this->gauge = $gauge;
        $this->range = $range;
    }

    static function asNew(string $name, GameId $gameId, Position $position, int $range): DominationFlag {
        return new DominationFlag($name, $gameId, $position, DominationFlagGauge::asNew(), $range);
    }

    public function makeProgress(TeamId $teamId, int $value): void {
        if ($this->gauge->isEmpty()) {
            $this->gauge->add($teamId, $value);
            return;
        }

        $owingTeamId = $this->gauge->getOwingTeamId();
        if ($owingTeamId->equals($teamId)) {
            if ($this->gauge->isOccupied()) return;

            $this->gauge->add($teamId, $value);
        } else {
            $this->gauge->reduce($teamId, $value);
        }
    }

    /**
     * @return Position
     */
    public function getPosition(): Position {
        return $this->position;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return DominationFlagGauge
     */
    public function getGauge(): DominationFlagGauge {
        return $this->gauge;
    }

    /**
     * @return GameId
     */
    public function getGameId(): GameId {
        return $this->gameId;
    }

    /**
     * @return int
     */
    public function getRange(): int {
        return $this->range;
    }
}