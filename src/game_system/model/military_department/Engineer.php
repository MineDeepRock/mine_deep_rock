<?php


namespace game_system\model\military_department;


use game_system\model\GadgetType;
use gun_system\models\GunType;
use pocketmine\entity\Effect;

class Engineer extends MilitaryDepartment
{
    public function __construct() {
        parent::__construct("Engineer",
            [
                GunType::LMG(),
            ],
            [
                GadgetType::AmmoBox()
            ],
            [
                Effect::HEALTH_BOOST
            ]
        );
    }
}