<?php


namespace gun_system\models\revolver;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadingType;
use pocketmine\scheduler\TaskScheduler;

class RevolverMk6 extends Revolver
{
    public function __construct() {
        parent::__construct(
            new BulletDamage(53,15),
            new GunRate(3.3),
            new BulletSpeed(230),
            0,
            new MagazineReloadingType(6,2.85),
            new EffectiveRange(0,8),
            new GunPrecision(97,90));
    }
}