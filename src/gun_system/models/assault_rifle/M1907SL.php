<?php


namespace gun_system\models\assault_rifle;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\ReloadDuration;
use pocketmine\scheduler\TaskScheduler;

class M1907SL extends AssaultRifle
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(new BulletDamage(42,23), new GunRate(5), new BulletSpeed(57), 20, 0, new ReloadDuration(2.3), new EffectiveRange(0,25), new GunPrecision(95), $scheduler);
    }
}