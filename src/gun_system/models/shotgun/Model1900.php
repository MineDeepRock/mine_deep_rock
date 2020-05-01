<?php


namespace gun_system\models\shotgun;


use gun_system\EffectiveRangeLoader;
use gun_system\models\attachment\bullet\ShotgunBulletType;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\ClipReloadingType;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;

class Model1900 extends Shotgun
{
    public function __construct(ShotgunBulletType $bulletType) {
        parent::__construct($bulletType,
            12, new BulletDamage(13, 2),
            new GunRate(5),
            new BulletSpeed(500),
            2, new ClipReloadingType(2,2,2.4,3.2),
            EffectiveRangeLoader::getInstance()->ranges["Model1900"],
            new GunPrecision(90,90));
    }
}