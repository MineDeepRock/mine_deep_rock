<?php


namespace gun_system\models\sniper_rifle;


use Closure;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\GunType;
use gun_system\models\ReloadDuration;
use gun_system\models\shotgun\attachment\scope\IronSightForSG;
use gun_system\models\shotgun\attachment\scope\ShotgunScope;
use gun_system\models\sniper_rifle\attachment\scope\IronSightForSR;
use gun_system\models\sniper_rifle\attachment\scope\SniperRifleScope;
use pocketmine\scheduler\TaskScheduler;

class SniperRifle extends Gun
{
    private $scope;

    public function __construct(BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, int $bulletCapacity, float $reaction, ReloadDuration $reloadDuration, EffectiveRange $effectiveRange, GunPrecision $precision, TaskScheduler $scheduler) {
        $this->setScope(new IronSightForSR());

        parent::__construct(GunType::SniperRifle(), $bulletDamage, $rate, $bulletSpeed, $bulletCapacity, $reaction, $reloadDuration, $effectiveRange, $precision, $scheduler);
    }

    /**
     * @return SniperRifleScope
     */
    public function getScope() :SniperRifleScope {
        return $this->scope;
    }

    /**
     * @param SniperRifleScope $scope
     */
    public function setScope(SniperRifleScope $scope): void {
        $this->scope = $scope;
    }
}