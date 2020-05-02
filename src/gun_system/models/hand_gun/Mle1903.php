<?php


namespace gun_system\models\hand_gun;


use gun_system\EffectiveRangeLoader;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadingType;

class Mle1903 extends HandGun
{
    const NAME = "Mle1903";

    public function __construct() {
        parent::__construct(
            new BulletDamage(30, 13),
            new GunRate(3.5),
            new BulletSpeed(350),
            0, new MagazineReloadingType(7, 2),
            EffectiveRangeLoader::getInstance()->ranges[self::NAME],
            new GunPrecision(98, 95));
    }
}