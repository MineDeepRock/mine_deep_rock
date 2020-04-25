<?php


namespace gun_system\models\hand_gun;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadController;
use pocketmine\scheduler\TaskScheduler;

class P08 extends HandGun
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(new BulletDamage(30, 15),
            new GunRate(5),
            new BulletSpeed(350),
            0, new MagazineReloadController(8, 1.3),
            new EffectiveRange(0, 10),
            new GunPrecision(98, 95),
            $scheduler);
    }
}