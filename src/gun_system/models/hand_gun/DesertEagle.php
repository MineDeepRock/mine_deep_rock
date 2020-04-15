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
        parent::__construct(10, new GunRate(0.7), new BulletSpeed(40), 8, 7, new ReloadDuration(3), 13, new GunPrecision(95), $scheduler);
    }
}