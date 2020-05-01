<?php


namespace game_system\model\map;


use game_system\model\Coordinate;

class ApocalypticCity extends TeamDeathMatchMap
{
    public const NAME = "ApocalypticCity";

    public function __construct() {
        parent::__construct(self::NAME, "",
            [
                new Coordinate(-182, 12, -134),
                new Coordinate(-172, 31, -103),
                new Coordinate(-165, 12, -94),
            ],
            [
                new Coordinate(-117, 12, -82),
                new Coordinate(-142, 17, -100),
                new Coordinate(-124, 12, -104),
            ]);
    }
}