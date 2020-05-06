<?php


namespace game_system\model;


class FlareBox
{
    const NAME = "";
    private $secondLimit;
    private $playerHit = [];
    private $coordinate;

    public function __construct(int $secondLimit, Coordinate $coordinate) {
        $this->secondLimit = $secondLimit;
        $this->coordinate = $coordinate;
    }

    /**
     * @return Coordinate
     */
    public function getCoordinate(): Coordinate {
        return $this->coordinate;
    }

}