<?php


namespace gun_system\models\assault_rifle;


use gun_system\EffectiveRangeLoader;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadingType;

class Ribeyrolles extends AssaultRifle
{
    const NAME = "Ribeyrolles";

    public function __construct() {
        parent::__construct(
            new BulletDamage(28, 17),
            new GunRate(9),
            new BulletSpeed(520),
            0,
            new MagazineReloadingType(25, 2),
            EffectiveRangeLoader::getInstance()->ranges[self::NAME],
            new GunPrecision(95, 90));
    }
}