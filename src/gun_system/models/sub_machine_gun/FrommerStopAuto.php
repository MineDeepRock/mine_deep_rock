<?php


namespace gun_system\models\sub_machine_gun;


use gun_system\EffectiveRangeLoader;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadingType;
use gun_system\models\OverheatRate;

class FrommerStopAuto extends SubMachineGun
{
    const NAME = "FrommerStopAuto";

    public function __construct() {
        parent::__construct(
            new BulletDamage(23),
            new GunRate(15),
            new BulletSpeed(350),
            new MagazineReloadingType(112,15, 1.25),
            EffectiveRangeLoader::getInstance()->ranges[self::NAME],
            new GunPrecision(98, 95),
            new OverheatRate(0));
    }
}