<?php


namespace game_system\model;


class SmokeGrenade extends Grenade
{
    public function __construct() {
        parent::__construct("SmokeGrenade", 8, 3,15);
    }
}