<?php


namespace gun_system\models\light_machine_gun;


use gun_system\EffectiveRangeLoader;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadingType;
use gun_system\models\OverheatRate;

class Chauchat extends LightMachineGun
{
    const NAME = "Chauchat";

    public function __construct() {
        parent::__construct(
            new BulletDamage(38),
            new GunRate(6),
            new BulletSpeed(720),
            new MagazineReloadingType(80,20, 3),
            EffectiveRangeLoader::getInstance()->ranges[self::NAME],
            new GunPrecision(97, 75),
            new OverheatRate(0));
    }
}