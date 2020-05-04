<?php


namespace game_system\model\military_department;


use gun_system\models\GunType;
use pocketmine\entity\Effect;

class AssaultSoldier extends MilitaryDepartment
{
    public function __construct() {
        parent::__construct("AssaultSoldier",
            [
                GunType::AssaultRifle(),
                GunType::Shotgun(),
            ],
            [],
            [
                Effect::SPEED
            ]
        );
    }
}