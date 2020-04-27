<?php


namespace gun_system\models\revolver;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\OneByOneReloadController;
use pocketmine\scheduler\TaskScheduler;

class NagantRevolver extends Revolver
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(
            new BulletDamage(40,23),
            new GunRate(3.3),
            new BulletSpeed(335),
            0,
            new OneByOneReloadController(7,1.3),
            new EffectiveRange(0,15),
            new GunPrecision(97,90),
            $scheduler);
    }
}