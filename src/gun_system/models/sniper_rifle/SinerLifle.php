<?php


namespace gun_system\models\sniper_rifle;


use gun_system\models\BulletSpeed;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\GunType;
use gun_system\models\ReloadDuration;
use pocketmine\scheduler\TaskScheduler;

class SinerLifle extends Gun
{
    public function __construct(float $attackPower, GunRate $rate, BulletSpeed $bulletSpeed, int $bulletCapacity, float $reaction, ReloadDuration $reloadDuration, int $range, GunPrecision $accurate, TaskScheduler $scheduler) {
        parent::__construct(GunType::SniperRifle(), $attackPower, $rate, $bulletSpeed, $bulletCapacity, $reaction, $reloadDuration, $range, $accurate, $scheduler);
    }

}