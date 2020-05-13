<?php


namespace game_system\model\military_department;


use gun_system\models\assault_rifle\M1907SL;
use gun_system\models\GunType;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;

class AssaultSoldier extends MilitaryDepartment
{
    public function __construct() {
        parent::__construct("AssaultSoldier",
            "突撃兵",
            [
                GunType::AssaultRifle(),
                GunType::Shotgun(),
            ],
            [],
            [
                new EffectInstance(Effect::getEffect(EFFECT::SPEED), null, 0, false)
            ],
            M1907SL::NAME
        );
    }

    function getDescription(): string {
        return "武器:AR,SG\nガジェット:\nエフェクト:移動速度上昇";
    }
}