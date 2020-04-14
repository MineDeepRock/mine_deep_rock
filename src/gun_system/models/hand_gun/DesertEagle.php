<?php


namespace gun_system\models\hand_gun;


use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\ReloadDuration;
use pocketmine\scheduler\TaskScheduler;


class DesertEagle extends HandGun
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(10, new GunRate(1), new BulletSpeed(5), 8, 4, new ReloadDuration(3), 10, new GunPrecision(95), $scheduler);
    }
}