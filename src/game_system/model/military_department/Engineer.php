<?php


namespace game_system\model\military_department;


use game_system\model\GadgetType;
use gun_system\models\GunType;
use gun_system\models\light_machine_gun\Chauchat;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;

class Engineer extends MilitaryDepartment
{
    public function __construct() {
        parent::__construct("Engineer",
            "工兵",
            [
                GunType::LMG(),
            ],
            [
                GadgetType::AmmoBox(),
                GadgetType::FlameBottle(),
                GadgetType::Sandbag(),
            ],
            [],
            Chauchat::NAME

        );
    }
}