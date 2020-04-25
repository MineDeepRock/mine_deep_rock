<?php


namespace game_system\model\map;


class RealisticWWIBattlefieldExtended extends TeamDeathMatchMap
{
    public function __construct() {
        parent::__construct("RealisticWWIBattlefieldExtended", "BioPowered", [],[]);
    }
}