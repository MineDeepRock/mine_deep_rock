<?php


namespace gun_system\models\shotgun;


use gun_system\models\attachment\bullet\ShotgunBulletType;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\OneByOneReloadController;
use pocketmine\scheduler\TaskScheduler;

class Model10A extends Shotgun
{
    public function __construct(ShotgunBulletType $bulletType, TaskScheduler $scheduler) {
        parent::__construct($bulletType, 12, new BulletDamage(12, 6), new GunRate(1.3), new BulletSpeed(333),  2, new OneByOneReloadController(6,0.5), new EffectiveRange(0, 20), new GunPrecision(80,76), $scheduler);
    }
}