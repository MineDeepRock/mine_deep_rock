<?php


namespace game_system\model;


abstract class Box
{
    const NAME = "";
    private $secondLimit;
    protected $playerUsed = [];
    private $coordinate;

    public function __construct(int $secondLimit, Coordinate $coordinate) {
        $this->secondLimit = $secondLimit;
        $this->coordinate = $coordinate;
    }

    /**
     * @return int
     */
    public function getSecondLimit(): int {
        return $this->secondLimit;
    }

    /**
     * @return array
     */
    public function getPlayerUsed(): array {
        return $this->playerUsed;
    }

    /**
     * @return Coordinate
     */
    public function getCoordinate(): Coordinate {
        return $this->coordinate;
    }
}