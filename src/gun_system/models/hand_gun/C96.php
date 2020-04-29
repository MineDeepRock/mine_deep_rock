<?php


namespace gun_system\models\hand_gun;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\OneByOneReloadingType;
use pocketmine\scheduler\TaskScheduler;

class C96 extends HandGun
{
    public function __construct() {
        parent::__construct(new BulletDamage(28, 15),
            new GunRate(5),
            new BulletSpeed(440),
            0, new OneByOneReloadingType(10, 0.25),
            new EffectiveRange(0, 15),
            new GunPrecision(98, 95));
    }
}