<?php


namespace game_system\model\map;


use game_system\model\Coordinate;

class WaterfrontHome extends TeamDeathMatchMap
{
    public const NAME = "WaterfrontHome";

    public function __construct() {
        parent::__construct(self::NAME, "",
            [
                new Coordinate(36, 27, 48),
                new Coordinate(52, 36, 51),
            ],
            [
                new Coordinate(24, 27, 30),
                new Coordinate(24, 44, 53),
            ]);
    }
}