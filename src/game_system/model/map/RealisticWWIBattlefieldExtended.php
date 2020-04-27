<?php


namespace game_system\model\map;


use game_system\model\Coordinate;

class RealisticWWIBattlefieldExtended extends TeamDeathMatchMap
{
    public const NAME = "RealisticWWIBattlefieldExtended";

    public function __construct() {
        parent::__construct(self::NAME, "BioPowered", [
            new Coordinate(9, 4, -66),
            new Coordinate(4, 4, -51),
            new Coordinate(-49, 4, -61),
            new Coordinate(-28, 4, -42),
            new Coordinate(3, 4, -42),
            new Coordinate(28, 4, -50),
            new Coordinate(-26, 9, -24),
            new Coordinate(-10, 9, -38),
            new Coordinate(-75, 10, -49)
        ], [
            new Coordinate(-70, 9, 55),
            new Coordinate(-74, 9, 36),
            new Coordinate(-45, 5, 39),
            new Coordinate(-20, 4, 57),
            new Coordinate(-13, 5, 56),
            new Coordinate(-7, 9, 38),
            new Coordinate(53, 5, 48),
            new Coordinate(46, 5, 48),
            new Coordinate(29, 8, 39),
            new Coordinate(-13, 5, 30)
        ]);
    }
}