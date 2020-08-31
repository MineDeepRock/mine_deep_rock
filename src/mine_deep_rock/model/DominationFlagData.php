<?php


namespace mine_deep_rock\model;


use pocketmine\level\Position;

class DominationFlagData
{
    private $name;
    private $position;
    private $range;

    public function __construct(string $name, Position $position,int $range) {
        $this->name = $name;
        $this->position = $position;
        $this->range = $range;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return Position
     */
    public function getPosition(): Position {
        return $this->position;
    }

    /**
     * @return int
     */
    public function getRange(): int {
        return $this->range;
    }

}