<?php


namespace gun_system\models\sniper_rifle;


use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\ReloadDuration;
use pocketmine\scheduler\TaskScheduler;

class Gehenna extends SniperRifle
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(20, new GunRate(0.5), new BulletSpeed(80), 5, 5, new ReloadDuration(3), 25, new GunPrecision(98.5), $scheduler);
    }

}