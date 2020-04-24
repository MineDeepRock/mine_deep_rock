<?php


namespace gun_system\models\assault_rifle;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\ClipReloadController;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use pocketmine\scheduler\TaskScheduler;

class CeiRigotti extends AssaultRifle
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(new BulletDamage(38,28), new GunRate(5), new BulletSpeed(700), 0,  new ClipReloadController(10,5,1.5,0.5), new EffectiveRange(0,25), new GunPrecision(98.8,97), $scheduler);
    }
}