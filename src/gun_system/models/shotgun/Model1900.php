<?php


namespace gun_system\models\shotgun;


use gun_system\EffectiveRangeLoader;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\ClipReloadingType;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;

class Model1900 extends Shotgun
{
    const NAME = "Model1900";

    public function __construct() {
        parent::__construct(
            12, new BulletDamage(13),
            new GunRate(20),
            new BulletSpeed(500),
            2, new ClipReloadingType(2, 2, 2.4, 3.2),
            EffectiveRangeLoader::getInstance()->ranges[self::NAME],
            new GunPrecision(90, 90));
    }
}