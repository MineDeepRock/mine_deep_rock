<?php


namespace gun_system\models\sniper_rifle;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\GunType;
use gun_system\models\ReloadingType;
use gun_system\models\sniper_rifle\attachment\scope\IronSightForSR;
use gun_system\models\sniper_rifle\attachment\scope\SniperRifleScope;
use pocketmine\scheduler\TaskScheduler;

class SniperRifle extends Gun
{
    private $scope;

    public function __construct(BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, float $reaction, ReloadingType $reloadingType, EffectiveRange $effectiveRange, GunPrecision $precision) {
        $this->setScope(new IronSightForSR());

        parent::__construct(GunType::SniperRifle(), $bulletDamage, $rate, $bulletSpeed, $reaction, $reloadingType, $effectiveRange, $precision);
    }

    /**
     * @return SniperRifleScope
     */
    public function getScope(): SniperRifleScope {
        return $this->scope;
    }

    /**
     * @param SniperRifleScope $scope
     */
    public function setScope(SniperRifleScope $scope): void {
        $this->scope = $scope;
    }
}