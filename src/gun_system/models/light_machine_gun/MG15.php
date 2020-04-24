<?php


namespace gun_system\models\light_machine_gun;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadController;
use pocketmine\scheduler\TaskScheduler;

class MG15 extends LightMachineGun
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(new OverheatRate(3), new BulletDamage(28, 21), new GunRate(8.3), new BulletSpeed(870),  new MagazineReloadController(100,4.5), new EffectiveRange(0, 18), new GunPrecision(97,75), $scheduler);
    }
}