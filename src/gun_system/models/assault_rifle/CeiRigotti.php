<?php


namespace gun_system\models\assault_rifle;


use gun_system\EffectiveRangeLoader;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\ClipReloadingType;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;

class CeiRigotti extends AssaultRifle
{
    const NAME = "CeiRigotti";

    public function __construct() {
        parent::__construct(
            new BulletDamage(38, 28),
            new GunRate(5),
            new BulletSpeed(700),
            0, new ClipReloadingType(10, 5, 1.5, 0.5),
            EffectiveRangeLoader::getInstance()->ranges[self::NAME],
            new GunPrecision(95, 90));
    }
}