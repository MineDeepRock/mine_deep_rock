<?php


namespace gun_system\models\revolver;


use gun_system\EffectiveRangeLoader;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\OneByOneReloadingType;

class NagantRevolver extends Revolver
{
    const NAME = "NagantRevolver";

    public function __construct() {
        parent::__construct(
            new BulletDamage(40, 23),
            new GunRate(3.3),
            new BulletSpeed(335),
            0,
            new OneByOneReloadingType(7, 1.3),
            EffectiveRangeLoader::getInstance()->ranges[self::NAME],
            new GunPrecision(97, 90));
    }
}