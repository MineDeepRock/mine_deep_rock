<?php


namespace mine_deep_rock\model;


use pocketmine\level\Position;

class DominationFlagData
{
    private $name;
    private $position;

    public function __construct(string $name, Position $position) {
        $this->name = $name;
        $this->position = $position;
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

}