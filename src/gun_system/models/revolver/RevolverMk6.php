<?php


namespace gun_system\models\revolver;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadController;
use gun_system\models\ReloadController;
use pocketmine\scheduler\TaskScheduler;

class RevolverMk6 extends Revolver
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(
            new BulletDamage(53,15),
            new GunRate(3.3),
            new BulletSpeed(230),
            0,
            new MagazineReloadController(6,2.85),
            new EffectiveRange(0,8),
            new GunPrecision(97,90),
            $scheduler);
    }
}