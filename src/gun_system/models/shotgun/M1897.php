<?php


namespace gun_system\models\shotgun;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\ReloadDuration;
use pocketmine\scheduler\TaskScheduler;

class M1897 extends Shotgun
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(12, new BulletDamage(10, 5), new GunRate(1), new BulletSpeed(33), 5, 2, new ReloadDuration(2), new EffectiveRange(0, 15), new GunPrecision(80), $scheduler);
    }
}