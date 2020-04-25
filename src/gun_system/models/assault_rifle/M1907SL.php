<?php


namespace gun_system\models\assault_rifle;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadController;
use pocketmine\scheduler\TaskScheduler;

class M1907SL extends AssaultRifle
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(
            new BulletDamage(42, 23),
            new GunRate(5),
            new BulletSpeed(570),
            0,
            new MagazineReloadController(20, 2.3),
            new EffectiveRange(0, 25),
            new GunPrecision(95, 90),
            $scheduler);
    }
}