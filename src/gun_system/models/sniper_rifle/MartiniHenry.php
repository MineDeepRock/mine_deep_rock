<?php


namespace gun_system\models\sniper_rifle;


use gun_system\EffectiveRangeLoader;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\OneByOneReloadingType;

class MartiniHenry extends SniperRifle
{
    public function __construct() {
        parent::__construct(new BulletDamage(112, 70),
            new GunRate(0.4),
            new BulletSpeed(440),
            3, new OneByOneReloadingType(1, 2.3),
            EffectiveRangeLoader::getInstance()->ranges["MartiniHenry"],
            new GunPrecision(99.5, 80));
    }
}