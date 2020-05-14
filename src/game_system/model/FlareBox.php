<?php


namespace game_system\model;


class FlareBox extends Box
{
    const NAME = "FlareBox";
    const SECOND_LIMIT = 40;

    public function __construct(Coordinate $coordinate) {
        parent::__construct($coordinate);
    }
}