<?php


namespace gun_system\models\revolver;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\GunType;
use gun_system\models\ReloadingType;
use gun_system\models\revolver\attachment\IronSightForRevolver;
use gun_system\models\revolver\attachment\RevolverScope;
use pocketmine\scheduler\TaskScheduler;

class Revolver extends Gun
{
    private $scope;

    public function __construct(BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, float $reaction, ReloadingType $reloadingType, EffectiveRange $effectiveRange, GunPrecision $precision) {
        $this->setScope(new IronSightForRevolver());
        parent::__construct(GunType::Revolver(), $bulletDamage, $rate, $bulletSpeed, $reaction, $reloadingType, $effectiveRange, $precision);
    }


    /**
     * @return RevolverScope
     */
    public function getScope(): RevolverScope {
        return $this->scope;
    }

    /**
     * @param RevolverScope $scope
     */
    public function setScope(RevolverScope $scope): void {
        $this->scope = $scope;
    }
}