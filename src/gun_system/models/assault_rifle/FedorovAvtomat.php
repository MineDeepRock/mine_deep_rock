<?php


namespace gun_system\models\assault_rifle;


use gun_system\EffectiveRangeLoader;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadingType;

class FedorovAvtomat extends AssaultRifle
{
    const NAME = "FedorovAvtomat";

    public function __construct() {
        parent::__construct(
            new BulletDamage(28, 19),
            new GunRate(7.5),
            new BulletSpeed(570),
            0, new MagazineReloadingType(26, 2.7),
            EffectiveRangeLoader::getInstance()->ranges[self::NAME],
            new GunPrecision(95, 90));
    }
}