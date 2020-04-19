<?php


namespace gun_system\models\sniper_rifle;


use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\ReloadDuration;
use pocketmine\scheduler\TaskScheduler;

class SMLEMK3 extends SniperRifle
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(100, new GunRate(0.8), new BulletSpeed(74), 5, 2.5, new ReloadDuration(1.7), 55, new GunPrecision(98.5), $scheduler);
    }

}