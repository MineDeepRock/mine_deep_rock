<?php


namespace game_system\model;


class AmmoBox extends Box
{
    const NAME = "AmmoBox";
    const SECOND_LIMIT = 40;

    public function __construct(Coordinate $coordinate) {
        parent::__construct($coordinate);
    }
}