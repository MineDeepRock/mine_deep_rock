<?php


namespace gun_system\models\hand_gun;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadingType;
use pocketmine\scheduler\TaskScheduler;

class P08 extends HandGun
{
    public function __construct() {
        parent::__construct(new BulletDamage(30, 15),
            new GunRate(5),
            new BulletSpeed(350),
            0, new MagazineReloadingType(8, 1.3),
            new EffectiveRange(0, 10),
            new GunPrecision(98, 95));
    }
}