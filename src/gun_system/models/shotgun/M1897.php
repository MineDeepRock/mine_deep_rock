<?php


namespace gun_system\models\shotgun;


use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\ReloadDuration;
use pocketmine\scheduler\TaskScheduler;

class M1897 extends Shotgun
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(4,5, new GunRate(2), new BulletSpeed(35), 8, 7.5, new ReloadDuration(2), 8, new GunPrecision(90), $scheduler);
    }
}