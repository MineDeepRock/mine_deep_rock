<?php


namespace gun_system\models\assault_rifle;


use gun_system\models\BulletSpeed;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\GunType;
use gun_system\models\ReloadDuration;
use pocketmine\scheduler\TaskScheduler;

abstract class AssaultRifle extends Gun
{
    public function __construct(float $bulletPower, GunRate $rate, BulletSpeed $bulletSpeed, int $bulletCapacity, float $reaction, ReloadDuration $reloadDuration, int $range, GunPrecision $precision, TaskScheduler $scheduler) {
        parent::__construct(GunType::AssaultRifle(),$bulletPower, $rate, $bulletSpeed, $bulletCapacity, $reaction, $reloadDuration, $range, $precision, $scheduler);
    }
}