<?php


namespace game_system\model;


class FragGrenade extends Grenade
{
    public function __construct() {
        parent::__construct("FragGrenade", 8, 3);
    }
}