<?php


namespace gun_system\models\sub_machine_gun;


use gun_system\models\BulletSpeed;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\ReloadDuration;
use pocketmine\scheduler\TaskScheduler;

class Automatico extends SubMachineGun
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(23,  new GunRate(15), new BulletSpeed(55), 25, 1, new ReloadDuration(2.1), 20, new GunPrecision(85), $scheduler);
    }
}