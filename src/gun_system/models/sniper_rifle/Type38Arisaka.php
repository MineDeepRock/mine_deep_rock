<?php


namespace gun_system\models\sniper_rifle;


use gun_system\EffectiveRangeLoader;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\ClipReloadingType;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;

class Type38Arisaka extends SniperRifle
{
    public function __construct() {
        parent::__construct(
            new BulletDamage(100, 70),
            new GunRate(0.95),
            new BulletSpeed(770),
            2.5, new ClipReloadingType(5, 5, 0.7, 0.7),
            EffectiveRangeLoader::getInstance()->ranges["Type38Arisaka"],
            new GunPrecision(99.5, 80));
    }
}