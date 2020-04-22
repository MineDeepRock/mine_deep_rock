<?php


namespace gun_system\models\assault_rifle;


use gun_system\models\assault_rifle\attachiment\magazine\AssaultRifleMagazine;
use gun_system\models\assault_rifle\attachiment\scope\AssaultRifleScope;
use gun_system\models\assault_rifle\attachiment\scope\IronSightForAR;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\GunType;
use gun_system\models\ReloadDuration;
use pocketmine\scheduler\TaskScheduler;

abstract class AssaultRifle extends Gun
{
    private $scope;
    private $magazine;

    public function __construct(BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, int $bulletCapacity, float $reaction, ReloadDuration $reloadDuration, EffectiveRange $effectiveRange, GunPrecision $precision, TaskScheduler $scheduler) {
        $this->setScope(new IronSightForAR());
        parent::__construct(GunType::AssaultRifle(),$bulletDamage, $rate, $bulletSpeed, $bulletCapacity, $reaction, $reloadDuration, $effectiveRange, $precision, $scheduler);
    }

    /**
     * @return AssaultRifleScope
     */
    public function getScope() :AssaultRifleScope {
        return $this->scope;
    }

    /**
     * @param AssaultRifleScope $scope
     */
    public function setScope(AssaultRifleScope $scope): void {
        $this->scope = $scope;
    }

    /**
     * @return AssaultRifleMagazine
     */
    public function getMagazine() :AssaultRifleMagazine {
        return $this->magazine;
    }

    /**
     * @param AssaultRifleMagazine $magazine
     */
    public function setMagazine(AssaultRifleMagazine $magazine): void {
        $this->bulletCapacity += $magazine->getAdditionalBullets();
        $this->reloadDuration = new ReloadDuration($this->reloadDuration->getSecond() + $magazine->getAdditionalReloadTime());
        $this->magazine = $magazine;
    }
}