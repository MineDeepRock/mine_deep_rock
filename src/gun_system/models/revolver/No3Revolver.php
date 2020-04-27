<?php


namespace gun_system\models\revolver;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadController;
use pocketmine\scheduler\TaskScheduler;

class No3Revolver extends Revolver
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(
            new BulletDamage(53,13),
            new GunRate(2.7),
            new BulletSpeed(210),
            0,
            new MagazineReloadController(6,2.3),
            new EffectiveRange(0,8),
            new GunPrecision(97,90),
            $scheduler);
    }
}