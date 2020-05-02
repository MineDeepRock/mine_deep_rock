<?php


namespace gun_system\models\hand_gun;


use gun_system\EffectiveRangeLoader;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadingType;

class P08 extends HandGun
{
    const NAME = "P08";

    public function __construct() {
        parent::__construct(
            new BulletDamage(30),
            new GunRate(5),
            new BulletSpeed(350),
            0, new MagazineReloadingType(8, 1.3),
            EffectiveRangeLoader::getInstance()->ranges[self::NAME],
            new GunPrecision(98, 95));
    }
}