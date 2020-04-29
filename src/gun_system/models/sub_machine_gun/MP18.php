<?php


namespace gun_system\models\sub_machine_gun;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadingType;
use pocketmine\scheduler\TaskScheduler;

class MP18 extends SubMachineGun
{
    public function __construct() {
        parent::__construct(new BulletDamage(28, 15),
            new GunRate(9),
            new BulletSpeed(420),
            new MagazineReloadingType(32, 2),
            new EffectiveRange(0, 15),
            new GunPrecision(98, 95));
    }
}