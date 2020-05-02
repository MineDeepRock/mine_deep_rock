<?php


namespace gun_system\models\shotgun;


use gun_system\EffectiveRangeLoader;
use gun_system\models\attachment\bullet\ShotgunBulletType;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\OneByOneReloadingType;

class M1897 extends Shotgun
{
    const NAME = "M1897";

    public function __construct(ShotgunBulletType $bulletType) {
        parent::__construct(
            $bulletType,
            12,
            new BulletDamage(10, 2),
            new GunRate(2.3),
            new BulletSpeed(333),
            2, new OneByOneReloadingType(5, 0.5),
            EffectiveRangeLoader::getInstance()->ranges[self::NAME],
            new GunPrecision(90, 90));
    }
}