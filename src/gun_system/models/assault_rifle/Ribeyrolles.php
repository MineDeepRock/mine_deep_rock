<?php


namespace gun_system\models\assault_rifle;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadController;
use pocketmine\scheduler\TaskScheduler;

class Ribeyrolles extends AssaultRifle
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(
            new BulletDamage(28,17),
            new GunRate(9),
            new BulletSpeed(520),
            0,
            new MagazineReloadController(25,2),
            new EffectiveRange(0,15),
            new GunPrecision(95, 90),
            $scheduler);
    }
}