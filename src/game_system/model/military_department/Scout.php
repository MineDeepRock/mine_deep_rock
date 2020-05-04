<?php


namespace game_system\model\military_department;


use gun_system\models\GunType;

class Scout extends MilitaryDepartment
{
    public function __construct() {
        parent::__construct("Scout",
            [
                GunType::SniperRifle(),
            ],
            [],
            []
        );
    }
}