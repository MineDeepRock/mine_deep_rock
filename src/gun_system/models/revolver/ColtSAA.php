<?php


namespace gun_system\models\revolver;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\ClipReloadController;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use pocketmine\scheduler\TaskScheduler;

class ColtSAA extends Revolver
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(
            new BulletDamage(60,30),
            new GunRate(3.7),
            new BulletSpeed(320),
            0,
            new ClipReloadController(6,6,6.3,1.3),
            new EffectiveRange(0,12),
            new GunPrecision(97,95),
            $scheduler);
    }
}