<?php


namespace gun_system\models\hand_gun;


use gun_system\models\assault_rifle\attachiment\magazine\HandGunMagazine;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\GunType;
use gun_system\models\hand_gun\attachment\scope\HandGunScope;
use gun_system\models\hand_gun\attachment\scope\IronSightForHG;
use gun_system\models\ReloadController;
use pocketmine\scheduler\TaskScheduler;

abstract class HandGun extends Gun
{
    private $scope;

    public function __construct(BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, float $reaction, ReloadController $reloadController, EffectiveRange $effectiveRange, GunPrecision $precision, TaskScheduler $scheduler) {
        $this->setScope(new IronSightForHG());
        parent::__construct(GunType::HandGun(),$bulletDamage, $rate, $bulletSpeed, $reaction, $reloadController, $effectiveRange, $precision, $scheduler);
    }


    /**
     * @return HandGunScope
     */
    public function getScope() :HandGunScope {
        return $this->scope;
    }

    /**
     * @param HandGunScope $scope
     */
    public function setScope(HandGunScope $scope): void {
        $this->scope = $scope;
    }
}