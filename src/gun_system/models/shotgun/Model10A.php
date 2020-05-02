<?php


namespace gun_system\models\shotgun;


use gun_system\EffectiveRangeLoader;
use gun_system\models\attachment\bullet\ShotgunBulletType;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\OneByOneReloadingType;

class Model10A extends Shotgun
{
    const NAME = "Model10A";

    public function __construct(ShotgunBulletType $bulletType) {
        parent::__construct(
            $bulletType,
            12,
            new BulletDamage(12, 2),
            new GunRate(1.3),
            new BulletSpeed(333),
            2.5, new OneByOneReloadingType(6, 0.5),
            EffectiveRangeLoader::getInstance()->ranges[self::NAME],
            new GunPrecision(92, 92));
    }
}