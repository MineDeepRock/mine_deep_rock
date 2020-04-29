<?php


namespace gun_system\models\hand_gun;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\GunType;
use gun_system\models\hand_gun\attachment\scope\HandGunScope;
use gun_system\models\hand_gun\attachment\scope\IronSightForHG;
use gun_system\models\ReloadingType;
use pocketmine\scheduler\TaskScheduler;

abstract class HandGun extends Gun
{
    private $scope;

    public function __construct(BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, float $reaction, ReloadingType $reloadingType, EffectiveRange $effectiveRange, GunPrecision $precision) {
        parent::__construct(GunType::HandGun(),$bulletDamage, $rate, $bulletSpeed, $reaction, $reloadingType, $effectiveRange, $precision);
    }
}