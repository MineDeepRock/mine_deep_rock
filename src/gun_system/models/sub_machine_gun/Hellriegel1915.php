<?php


namespace gun_system\models\sub_machine_gun;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\MagazineReloadController;
use pocketmine\scheduler\TaskScheduler;

class Hellriegel1915 extends SubMachineGun
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(new BulletDamage(26,15), new GunRate(11), new BulletSpeed(380), new MagazineReloadController(59,3.8), new EffectiveRange(0,12), new GunPrecision(98.5,97.5), $scheduler);
    }
}