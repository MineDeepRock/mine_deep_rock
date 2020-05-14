<?php


namespace game_system\model;


abstract class Box
{
    const NAME = "";
    const SECOND_LIMIT = 0;
    private $coordinate;

    public function __construct(Coordinate $coordinate) {
        $this->coordinate = $coordinate;
    }

    /**
     * @return Coordinate
     */
    public function getCoordinate(): Coordinate {
        return $this->coordinate;
    }
}