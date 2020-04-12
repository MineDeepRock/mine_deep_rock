<?php


namespace gun_system\models;


use pocketmine\scheduler\TaskScheduler;

class HandGun extends Gun
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(5, new GunRate(1), new BulletSpeed(10),10, 1, new ReloadDuration(3), 10,$scheduler);
    }
}