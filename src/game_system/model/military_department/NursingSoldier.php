<?php


namespace game_system\model\military_department;


use game_system\model\GadgetType;
use gun_system\models\GunType;
use gun_system\models\sub_machine_gun\MP18;
use pocketmine\entity\Effect;

class NursingSoldier extends MilitaryDepartment
{
    public function __construct() {
        parent::__construct("NursingSoldier",
            [
                GunType::SMG(),
            ],
            [
                GadgetType::MedicineBox()
            ],
            [],
            MP18::NAME
        );
    }
}