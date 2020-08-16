<?php


namespace mine_deep_rock\model;


use pocketmine\level\Position;

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
     * @var float
     */
    private $gauge;

    public function __construct(string $name, Position $position, int $gauge) {
        $this->name = $name;
        $this->position = $position;
        $this->gauge = $gauge;
    }

    static function asNew(string $name, Position $position): DominationFlag {
        return new DominationFlag($name, $position, 0);
    }

    public function makeProgress(int $value): void {
        $this->gauge += $value;
        if ($this->gauge > 100) {
            $this->gauge = 100;
        } else if ($this->gauge < -100) {
            $this->gauge = -100;
        }
    }

    public function resetGauge(): void {
        $this->gauge = 0;
    }

    public function isOccupied(): bool {
        return $this->gauge > 100 || $this->gauge < -100;
    }

    /**
     * @return float
     */
    public function getGauge(): float {
        return $this->gauge;
    }

    /**
     * @return Position
     */
    public function getPosition(): Position {
        return $this->position;
    }
}