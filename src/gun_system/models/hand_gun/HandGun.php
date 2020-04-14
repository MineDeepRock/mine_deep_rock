<?php


namespace gun_system\models\hand_gun;


use gun_system\models\BulletSpeed;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\ReloadDuration;
use pocketmine\scheduler\TaskScheduler;

abstract class HandGun extends Gun
{
    public function __construct(float $attackPower, GunRate $rate, BulletSpeed $bulletSpeed, int $bulletCapacity, float $reaction, ReloadDuration $reloadDuration, int $range, GunPrecision $accurate, TaskScheduler $scheduler) {
        parent::__construct($attackPower, $rate, $bulletSpeed, $bulletCapacity, $reaction, $reloadDuration, $range, $accurate, $scheduler);
    }
}