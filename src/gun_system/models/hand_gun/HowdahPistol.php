<?php


namespace gun_system\models\hand_gun;


use gun_system\EffectiveRangeLoader;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadingType;

class HowdahPistol extends HandGun
{
    public function __construct() {
        parent::__construct(new BulletDamage(53, 15),
            new GunRate(4),
            new BulletSpeed(230),
            0, new MagazineReloadingType(4, 3.3),
            EffectiveRangeLoader::getInstance()->ranges["HowdahPistol"],
            new GunPrecision(99, 95));
    }
}