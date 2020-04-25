<?php


namespace game_system\model\Maps;


use game_system\model\TeamDeathMatchMap;

class RealisticWWIBattlefieldExtended extends TeamDeathMatchMap
{
    public function __construct() {
        parent::__construct("RealisticWWIBattlefieldExtended", "BioPowered", [],[]);
    }
}