<?php


namespace gun_system\models\shotgun;


use gun_system\models\attachment\bullet\ShotgunBulletType;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\OneByOneReloadingType;
use pocketmine\scheduler\TaskScheduler;

class Model10A extends Shotgun
{
    public function __construct(ShotgunBulletType $bulletType) {
        parent::__construct($bulletType,
            12,
            new BulletDamage(12, 2),
            new GunRate(1.3),
            new BulletSpeed(333),
            2.5, new OneByOneReloadingType(6,0.5),
            new EffectiveRange(0, 20),
            new GunPrecision(92,92));
    }
}