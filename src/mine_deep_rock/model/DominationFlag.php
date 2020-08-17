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


    public function __construct(string $name, GameId $gameId, Position $position, DominationFlagGauge $gauge) {
        $this->name = $name;
        $this->position = $position;
        $this->gauge = $gauge;
    }

    static function asNew(string $name, GameId $gameId, Position $position): DominationFlag {
        return new DominationFlag($name, $gameId, $position, DominationFlagGauge::asNew());
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
}