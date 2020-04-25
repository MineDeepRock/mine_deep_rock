<?php


namespace gun_system\models\light_machine_gun;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadController;
use pocketmine\scheduler\TaskScheduler;

class BAR1918 extends LightMachineGun
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(new OverheatRate(0),
            new BulletDamage(26, 23),
            new GunRate(10),
            new BulletSpeed(820),
            new MagazineReloadController(20, 3),
            new EffectiveRange(0, 13),
            new GunPrecision(98, 75),
            $scheduler);
    }
}