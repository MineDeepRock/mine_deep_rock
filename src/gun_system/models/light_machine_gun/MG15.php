<?php


namespace gun_system\models\light_machine_gun;


use gun_system\EffectiveRangeLoader;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadingType;

class MG15 extends LightMachineGun
{
    public function __construct() {
        parent::__construct(new OverheatRate(3),
            new BulletDamage(28, 21),
            new GunRate(8.3),
            new BulletSpeed(870),
            new MagazineReloadingType(100, 4.5),
            EffectiveRangeLoader::getInstance()->ranges["MG15"],
            new GunPrecision(97, 75));
    }
}