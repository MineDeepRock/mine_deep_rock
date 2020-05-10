<?php


namespace game_system\model\map;


use game_system\model\Coordinate;

class ApocalypticCityForDomination extends TeamDominationMap
{
    public const NAME = "Apocalyptic City";

    public function __construct() {
        parent::__construct(self::NAME, "",
            [
                new Coordinate(36, 11, 28),
                new Coordinate(46, 18, -36),
                new Coordinate(11, 12, -42),
            ],
            [
                new Coordinate(-122, 18, 37),
                new Coordinate(-109, 18, 0),
                new Coordinate(-89, 18, -32),
            ],
            [
                new DominationFlag("A",new Coordinate(1, 11, 1),),
            ]
        );
    }
}