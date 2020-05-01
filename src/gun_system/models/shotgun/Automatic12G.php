<?php


namespace gun_system\models\shotgun;


use gun_system\EffectiveRangeLoader;
use gun_system\models\attachment\bullet\ShotgunBulletType;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\OneByOneReloadingType;

class Automatic12G extends Shotgun
{
    public function __construct(ShotgunBulletType $bulletType) {
        parent::__construct($bulletType,
            12,
            new BulletDamage(7.7, 1),
            new GunRate(4.2),
            new BulletSpeed(333),
            1, new OneByOneReloadingType(5,0.7),
            EffectiveRangeLoader::getInstance()->ranges["Automatic12G"],
            new GunPrecision(90,90));
    }
}