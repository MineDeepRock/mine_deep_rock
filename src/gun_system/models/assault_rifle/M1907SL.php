<?php


namespace gun_system\models\assault_rifle;


use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\ReloadDuration;
use pocketmine\scheduler\TaskScheduler;

class M1907SL extends AssaultRifle
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(42, new GunRate(2.5), new BulletSpeed(57), 20, 1, new ReloadDuration(2.3), 25, new GunPrecision(95), $scheduler);
    }
}