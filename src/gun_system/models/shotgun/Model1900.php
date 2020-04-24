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

class Model1900 extends Shotgun
{
    public function __construct(ShotgunBulletType $bulletType, TaskScheduler $scheduler) {
        parent::__construct($bulletType, 12, new BulletDamage(12.5, 6.25), new GunRate(5), new BulletSpeed(333),  1, new OneByOneReloadController(2,2.5), new EffectiveRange(0, 13), new GunPrecision(80,76), $scheduler);
    }
}