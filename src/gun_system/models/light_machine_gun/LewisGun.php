<?php


namespace gun_system\models\light_machine_gun;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\ReloadDuration;
use pocketmine\scheduler\TaskScheduler;

class LewisGun extends LightMachineGun
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(new OverheatRate(0), new BulletDamage(26, 20), new GunRate(8), new BulletSpeed(74), 47, new ReloadDuration(4.2), new EffectiveRange(0, 11), new GunPrecision(80), $scheduler);
    }
}