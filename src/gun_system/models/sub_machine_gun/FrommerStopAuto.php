<?php


namespace gun_system\models\sub_machine_gun;


use gun_system\EffectiveRangeLoader;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadingType;

class FrommerStopAuto extends SubMachineGun
{
    public function __construct() {
        parent::__construct(new BulletDamage(23, 12),
            new GunRate(15),
            new BulletSpeed(350),
            new MagazineReloadingType(15, 1.25),
            EffectiveRangeLoader::getInstance()->ranges["FrommerStopAuto"],
            new GunPrecision(98, 95));
    }
}