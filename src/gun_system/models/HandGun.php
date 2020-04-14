<?php


namespace gun_system\models;


use pocketmine\scheduler\TaskScheduler;

class HandGun extends Gun
{

    public static function getId(): int {
        return GunId::HAND_GUN;
    }

    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(5, new GunRate(1), new BulletSpeed(5), 10, 1, new ReloadDuration(3), 10, new GunPrecision(80),$scheduler);
    }
}