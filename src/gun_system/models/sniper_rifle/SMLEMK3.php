<?php


namespace gun_system\models\sniper_rifle;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\ReloadDetail;
use pocketmine\scheduler\TaskScheduler;

class SMLEMK3 extends SniperRifle
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(new BulletDamage(100,80), new GunRate(0.8), new BulletSpeed(740), 10, 2.5, new ReloadDetail(false,5,1.8,0.5), new EffectiveRange(10,50), new GunPrecision(99.5,80), $scheduler);
    }
}