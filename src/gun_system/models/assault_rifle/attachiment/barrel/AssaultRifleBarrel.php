<?php


namespace gun_system\models\assault_rifle\attachiment\barrel;


use gun_system\models\attachment\Barrel;
use gun_system\models\GunType;

class AssaultRifleBarrel extends Barrel
{
    public function __construct(string $name,$bulletDamagePer, $bulletSpeedPer, $precisionPer, $reactionPer) {
        parent::__construct($name, GunType::AssaultRifle(), $bulletDamagePer, $bulletSpeedPer, $precisionPer, $reactionPer);
    }
}