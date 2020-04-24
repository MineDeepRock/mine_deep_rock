<?php


namespace gun_system\models\sniper_rifle;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\OneByOneReloadController;
use pocketmine\scheduler\TaskScheduler;

class MartiniHenry extends SniperRifle
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(new BulletDamage(112,70), new GunRate(0.4), new BulletSpeed(440),  3, new OneByOneReloadController(1,2.3), new EffectiveRange(10,50), new GunPrecision(99.5,80), $scheduler);
    }
}