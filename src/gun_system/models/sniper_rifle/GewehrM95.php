<?php


namespace gun_system\models\sniper_rifle;


use gun_system\EffectiveRangeLoader;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\ClipReloadingType;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;

class GewehrM95 extends SniperRifle
{
    const NAME = "GewehrM95";

    public function __construct() {
        parent::__construct(
            new BulletDamage(90),
            new GunRate(1.1),
            new BulletSpeed(620),
            1, new ClipReloadingType(25,5, 5, 3, 3),
            EffectiveRangeLoader::getInstance()->ranges[self::NAME],
            new GunPrecision(99.5, 80));
    }
}