<?php


namespace gun_system\models\light_machine_gun;


use gun_system\EffectiveRangeLoader;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadingType;

class BAR1918 extends LightMachineGun
{
    public function __construct() {
        parent::__construct(new OverheatRate(0),
            new BulletDamage(26, 23),
            new GunRate(10),
            new BulletSpeed(820),
            new MagazineReloadingType(20, 3),
            EffectiveRangeLoader::getInstance()->ranges["BAR1918"],
            new GunPrecision(98, 75));
    }
}