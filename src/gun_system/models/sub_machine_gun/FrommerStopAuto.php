<?php


namespace gun_system\models\sub_machine_gun;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadingType;
use pocketmine\scheduler\TaskScheduler;

class FrommerStopAuto extends SubMachineGun
{
    public function __construct() {
        parent::__construct(new BulletDamage(23, 12),
            new GunRate(15),
            new BulletSpeed(350),
            new MagazineReloadingType(15, 1.25),
            new EffectiveRange(0, 5),
            new GunPrecision(98, 95));
    }
}