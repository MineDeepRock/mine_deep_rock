<?php


namespace game_system\model\map;


use game_system\model\Coordinate;

class VoForDomination extends TeamDominationMap
{
    public const NAME = "vo";

    public function __construct() {
        parent::__construct(self::NAME, "",
            [
                new Coordinate(-296, 36, -168),
                new Coordinate(-292, 36, -165),
                new Coordinate(-312, 34, -158),
                new Coordinate(-316, 34, -151),
                new Coordinate(-321, 35, -156),
                new Coordinate(-332, 35, -155),
                new Coordinate(-339, 35, -155),
                new Coordinate(-325, 35, -162),
            ],
            [
                new Coordinate(-298,29 , -256),
                new Coordinate(-305,29 , -266),
                new Coordinate(-317,33 , -274),
                new Coordinate(-305,30 , -279),
                new Coordinate(-297,31 , -289),
                new Coordinate(-298,29 , -282),
                new Coordinate(-288,29 , -282),
                new Coordinate(-288,29 , -279),
            ],
            [
                new DominationFlag("Trench", new Coordinate(-332, 25, -217)),
                new DominationFlag("Town", new Coordinate(-415, 28, -215)),
                new DominationFlag("Chai", new Coordinate(-367, 28, -269)),
            ]
        );
    }
}