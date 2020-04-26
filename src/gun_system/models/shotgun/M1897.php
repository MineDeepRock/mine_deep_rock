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

class M1897 extends Shotgun
{
    public function __construct(ShotgunBulletType $bulletType, TaskScheduler $scheduler) {
        parent::__construct($bulletType, 12, new BulletDamage(10, 5), new GunRate(2.3), new BulletSpeed(333),  2, new OneByOneReloadController(5,0.5), new EffectiveRange(0, 10), new GunPrecision(90,90), $scheduler);
    }
}