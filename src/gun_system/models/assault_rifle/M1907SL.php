<?php


namespace gun_system\models\assault_rifle;


use gun_system\EffectiveRangeLoader;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadingType;

class M1907SL extends AssaultRifle
{
    const NAME = "M1907SL";

    public function __construct() {
        parent::__construct(
            new BulletDamage(42, 23),
            new GunRate(5),
            new BulletSpeed(570),
            0,
            new MagazineReloadingType(20, 2.3),
            EffectiveRangeLoader::getInstance()->ranges[self::NAME],
            new GunPrecision(95, 90));
    }
}