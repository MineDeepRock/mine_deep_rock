<?php


namespace gun_system\models\sub_machine_gun;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadingType;
use pocketmine\scheduler\TaskScheduler;

class Hellriegel1915 extends SubMachineGun
{
    public function __construct() {
        parent::__construct(new BulletDamage(26, 15),
            new GunRate(11),
            new BulletSpeed(380),
            new MagazineReloadingType(59, 3.8),
            new EffectiveRange(0, 12),
            new GunPrecision(90, 85));
    }
}