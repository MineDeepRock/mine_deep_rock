<?php


namespace gun_system\models\hand_gun;


use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\ReloadDuration;
use pocketmine\scheduler\TaskScheduler;

class Mle1903 extends HandGun
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(30, new GunRate(3.5), new BulletSpeed(35), 7, 0, new ReloadDuration(3), 13, new GunPrecision(95), $scheduler);
    }
}