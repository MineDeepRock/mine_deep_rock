<?php


namespace gun_system\models\assault_rifle;


use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\ReloadDuration;
use pocketmine\scheduler\TaskScheduler;

class M1Garand extends AssaultRifle
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(7, new GunRate(3.5), new BulletSpeed(40), 8, 1, new ReloadDuration(1), 18, new GunPrecision(95), $scheduler);
    }
}