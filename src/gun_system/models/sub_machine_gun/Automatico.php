<?php


namespace gun_system\models\sub_machine_gun;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadController;
use pocketmine\scheduler\TaskScheduler;

class Automatico extends SubMachineGun
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(new BulletDamage(23, 13.5),
            new GunRate(15),
            new BulletSpeed(380),
            new MagazineReloadController(25, 2.1),
            new EffectiveRange(0, 5),
            new GunPrecision(95, 85),
            $scheduler);
    }
}