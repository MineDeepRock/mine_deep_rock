<?php


namespace gun_system\models\light_machine_gun;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\GunType;
use gun_system\models\ReloadDuration;
use pocketmine\scheduler\TaskScheduler;

class LightMachineGun extends Gun
{
    public function __construct(BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, int $bulletCapacity, ReloadDuration $reloadDuration, EffectiveRange $effectiveRange, GunPrecision $precision, TaskScheduler $scheduler) {
        parent::__construct(GunType::LMG(), $bulletDamage, $rate, $bulletSpeed, $bulletCapacity, 0.0, $reloadDuration, $effectiveRange, $precision, $scheduler);
    }

}