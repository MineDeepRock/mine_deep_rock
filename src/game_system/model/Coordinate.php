<?php


namespace game_system\model;


use ValueObject;

class Coordinate extends ValueObject
{
    private $x;
    private $y;
    private $z;

    public function __construct(int $x, int $y, int $z) {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    /**
     * @return int
     */
    public function getX(): int {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY(): int {
        return $this->y;
    }

    /**
     * @return int
     */
    public function getZ(): int {
        return $this->z;
    }
}