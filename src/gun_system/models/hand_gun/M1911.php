<?php


namespace gun_system\models\hand_gun;


use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\ReloadDuration;
use pocketmine\scheduler\TaskScheduler;

class M1911 extends HandGun
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(5, new GunRate(3), new BulletSpeed(27), 8, 1, new ReloadDuration(2), 10, new GunPrecision(90), $scheduler);
    }
}