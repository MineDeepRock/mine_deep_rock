<?php


namespace gun_system\models\sub_machine_gun;


use gun_system\models\assault_rifle\attachiment\magazine\SubMachineGunMagazine;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\GunType;
use gun_system\models\ReloadController;
use gun_system\models\sub_machine_gun\attachment\scope\IronSightForSMG;
use gun_system\models\sub_machine_gun\attachment\scope\SubMachineGunScope;
use pocketmine\scheduler\TaskScheduler;

class SubMachineGun extends Gun
{
    private $scope;
    private $magazine;

    public function __construct(BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, ReloadController $reloadController, EffectiveRange $effectiveRange, GunPrecision $precision, TaskScheduler $scheduler) {
        $this->setScope(new IronSightForSMG());
        parent::__construct(GunType::SMG(), $bulletDamage, $rate, $bulletSpeed, 0.0, $reloadController, $effectiveRange, $precision, $scheduler);
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
    /**
     * @param SubMachineGunMagazine $magazine
     */
    public function setMagazine(SubMachineGunMagazine $magazine): void {
        //$this->bulletCapacity += $magazine->getAdditionalBullets();
        //$this->reloadDuration = new ReloadDuration($this->reloadDuration->getSecond() + $magazine->getAdditionalReloadTime());
        //$this->magazine = $magazine;
    }
}