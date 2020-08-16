<?php


namespace mine_deep_rock\model;


use pocketmine\level\Position;
use team_game_system\model\TeamId;

class DominationFlag
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Position
     */
    private $position;

    /**
     * @var DominationFlagGauge
     */
    private $gauge;


    public function __construct(string $name, Position $position, DominationFlagGauge $gauge) {
        $this->name = $name;
        $this->position = $position;
        $this->gauge = $gauge;
    }

    static function asNew(string $name, Position $position): DominationFlag {
        return new DominationFlag($name, $position, DominationFlagGauge::asNew());
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

    public function resetGauge(): void {
        $this->gauge = [];
    }

    public function isOccupied(): bool {
        return $this->gauge->isOccupied();
    }

    /**
     * @return int
     */
    public function getGaugeAsInt(): int {
        return $this->gauge->asInt();
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
}