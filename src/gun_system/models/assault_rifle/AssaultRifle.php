<?php


namespace gun_system\models\assault_rifle;


use gun_system\models\assault_rifle\attachiment\scope\AssaultRifleScope;
use gun_system\models\assault_rifle\attachiment\scope\IronSightForAR;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\GunType;
use gun_system\models\ReloadingType;
use pocketmine\scheduler\TaskScheduler;

abstract class AssaultRifle extends Gun
{
    private $scope;

    public function __construct(BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, float $reaction, ReloadingType $reloadingType, EffectiveRange $effectiveRange, GunPrecision $precision) {
        $this->setScope(new IronSightForAR());
        parent::__construct(GunType::AssaultRifle(), $bulletDamage, $rate, $bulletSpeed, $reaction, $reloadingType, $effectiveRange, $precision);
    }

    /**
     * @return AssaultRifleScope
     */
    public function getScope(): AssaultRifleScope {
        return $this->scope;
    }

    /**
     * @param AssaultRifleScope $scope
     */
    public function setScope(AssaultRifleScope $scope): void {
        $this->scope = $scope;
    }
}