<?php


namespace game_system\model\military_department;


use game_system\model\GadgetType;
use gun_system\models\GunType;
use gun_system\models\sniper_rifle\SMLEMK3;

class Scout extends MilitaryDepartment
{
    public function __construct() {
        parent::__construct("Scout",
            "斥候兵",
            [
                GunType::SniperRifle(),
            ],
            [
                GadgetType::FlareBox(),
            ],
            [],
            SMLEMK3::NAME
        );
    }

    function getDescription(): string {
        return "武器:SR\nガジェット:フレア箱\nエフェクト:";
    }
}