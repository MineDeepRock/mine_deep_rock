<?php


namespace game_system\model;


class AmmoBox extends Box
{
    public function __construct(int $secondLimit, Coordinate $coordinate) {
        parent::__construct($secondLimit, $coordinate);
    }
}