<?php


namespace game_system\model;


class MedicineBox extends Box
{
    const NAME = "MedicineBox";
    const SECOND_LIMIT = 40;

    public function __construct(Coordinate $coordinate) {
        parent::__construct($coordinate);
    }
}