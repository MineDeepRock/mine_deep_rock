<?php


namespace gun_system\models\sniper_rifle;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\ClipReloadController;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use pocketmine\scheduler\TaskScheduler;

class Gewehr98 extends SniperRifle
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(new BulletDamage(100, 80),
            new GunRate(0.8),
            new BulletSpeed(880),
            2.5, new ClipReloadController(5, 5, 1.5, 0.5),
            new EffectiveRange(30, 80),
            new GunPrecision(99.5, 80),
            $scheduler);
    }
}