<?php


namespace game_system\model\military_department;


use game_system\model\GadgetType;
use gun_system\models\GunType;
use gun_system\models\light_machine_gun\Chauchat;
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
            ],
            Chauchat::NAME

        );
    }

    function getDescription(): string {
        return "武器 : LMG\nガジェット: 弾薬箱\nエフェクト : 体力 1.5倍";
    }
}