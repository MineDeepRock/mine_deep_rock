<?php


namespace game_system\model\map;


use pocketmine\math\Vector3;

class RealisticWWIBattlefieldExtended extends TeamDeathMatchMap
{
    public function __construct() {
        parent::__construct("RealisticWWIBattlefieldExtended", "BioPowered", [
            new Vector3(9, 4, -66),
            new Vector3(4, 4, -51),
            new Vector3(-49, 4, -61),
            new Vector3(-28, 4, -42),
            new Vector3(3, 4, -42),
            new Vector3(28, 4, -50),
            new Vector3(-26, 9, -24),
            new Vector3(-10, 9, -38),
            new Vector3(-75, 10, -49)
        ], [
            new Vector3(-70, 9, 55),
            new Vector3(-74, 9, 36),
            new Vector3(-45, 5, 39),
            new Vector3(-20, 4, 57),
            new Vector3(-13, 5, 56),
            new Vector3(-7, 9, 38),
            new Vector3(53, 5, 48),
            new Vector3(46, 5, 48),
            new Vector3(29, 8, 39),
            new Vector3(-13, 5, 30)
        ]);
    }
}