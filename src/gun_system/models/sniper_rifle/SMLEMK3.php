<?php


namespace gun_system\models\sniper_rifle;


use gun_system\EffectiveRangeLoader;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\ClipReloadingType;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;

class SMLEMK3 extends SniperRifle
{
    const NAME = "SMLEMK3";

    public function __construct() {
        parent::__construct(
            new BulletDamage(100, 80),
            new GunRate(0.8),
            new BulletSpeed(740),
            2.5, new ClipReloadingType(10, 5, 1.8, 0.5),
            EffectiveRangeLoader::getInstance()->ranges[self::NAME],
            new GunPrecision(99.5, 80));
    }
}