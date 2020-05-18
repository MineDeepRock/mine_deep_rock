<?php


namespace game_system\model;


use pocketmine\math\Vector3;
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

    public function toVector3(): Vector3 {
        return new Vector3(
          $this->x,
          $this->y,
          $this->z
        );
    }
}