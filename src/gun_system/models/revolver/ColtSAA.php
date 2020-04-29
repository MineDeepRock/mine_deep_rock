<?php


namespace gun_system\models\revolver;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\OneByOneReloadingType;
use pocketmine\scheduler\TaskScheduler;

class ColtSAA extends Revolver
{
    public function __construct() {
        parent::__construct(
            new BulletDamage(60,30),
            new GunRate(3.7),
            new BulletSpeed(320),
            0,
            new OneByOneReloadingType(6,1.2),
            new EffectiveRange(0,12),
            new GunPrecision(97,95));
    }
}