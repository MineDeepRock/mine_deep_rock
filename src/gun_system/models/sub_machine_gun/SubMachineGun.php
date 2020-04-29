<?php


namespace gun_system\models\sub_machine_gun;


use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\GunType;
use gun_system\models\ReloadingType;
use gun_system\models\sub_machine_gun\attachment\scope\IronSightForSMG;
use gun_system\models\sub_machine_gun\attachment\scope\SubMachineGunScope;
use pocketmine\scheduler\TaskScheduler;

class SubMachineGun extends Gun
{
    private $scope;

    public function __construct(BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, ReloadingType $reloadingType, EffectiveRange $effectiveRange, GunPrecision $precision) {
        $this->setScope(new IronSightForSMG());
        parent::__construct(GunType::SMG(), $bulletDamage, $rate, $bulletSpeed, 0.0, $reloadingType, $effectiveRange, $precision);
    }

    /**
     * @return SubMachineGunScope
     */
    public function getScope(): SubMachineGunScope {
        return $this->scope;
    }

    /**
     * @param SubMachineGunScope $scope
     */
    public function setScope(SubMachineGunScope $scope): void {
        $this->scope = $scope;
    }
}