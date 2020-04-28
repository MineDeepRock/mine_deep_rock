<?php


namespace gun_system\models\sniper_rifle;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\ClipReloadController;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use pocketmine\scheduler\TaskScheduler;

class Type38Arisaka extends SniperRifle
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(
            new BulletDamage(100, 70),
            new GunRate(0.95),
            new BulletSpeed(770),
            2.5, new ClipReloadController(5, 5, 0.7, 0.7),
            new EffectiveRange(30, 62),
            new GunPrecision(99.5, 80),
            $scheduler);
    }
}