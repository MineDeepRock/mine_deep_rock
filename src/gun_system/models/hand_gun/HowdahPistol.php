<?php


namespace gun_system\models\hand_gun;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadController;
use pocketmine\scheduler\TaskScheduler;

class HowdahPistol extends HandGun
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(new BulletDamage(53, 15),
            new GunRate(4),
            new BulletSpeed(230),
            0, new MagazineReloadController(4, 3.3),
            new EffectiveRange(0, 8),
            new GunPrecision(99, 95),
            $scheduler);
    }
}