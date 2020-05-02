<?php


namespace gun_system\models\revolver;


use gun_system\EffectiveRangeLoader;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\OneByOneReloadingType;

class ColtSAA extends Revolver
{
    const NAME = "ColtSAA";

    public function __construct() {
        parent::__construct(
            new BulletDamage(60, 30),
            new GunRate(3.7),
            new BulletSpeed(320),
            0,
            new OneByOneReloadingType(6, 1.2),
            EffectiveRangeLoader::getInstance()->ranges[self::NAME],
            new GunPrecision(97, 95));
    }
}