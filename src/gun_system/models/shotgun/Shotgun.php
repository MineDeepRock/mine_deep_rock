<?php


namespace gun_system\models\shotgun;


use gun_system\models\attachment\bullet\ShotgunBulletType;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\GunType;
use gun_system\models\ReloadController;
use gun_system\models\shotgun\attachment\scope\IronSightForSG;
use gun_system\models\shotgun\attachment\scope\ShotgunScope;
use pocketmine\scheduler\TaskScheduler;

abstract class Shotgun extends Gun
{
    private $scope;

    private $bulletType;
    private $pellets;

    public function __construct(ShotgunBulletType $bulletType,int $pellets, BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, float $reaction, ReloadController $reloadController, EffectiveRange $effectiveRange, GunPrecision $precision, TaskScheduler $scheduler) {
        $this->setScope(new IronSightForSG());

        $this->bulletType = $bulletType;
        $this->pellets = $pellets;

        if ($this->bulletType->equal(ShotgunBulletType::Slug())) {
            $bulletDamage = new BulletDamage($bulletDamage->getMaxDamage() * $this->pellets,$bulletDamage->getMinDamage() * $this->pellets);
            $effectiveRange = new EffectiveRange($effectiveRange->getStart(),$effectiveRange->getEnd()+10);
            $this->pellets = 1;
        }

        parent::__construct(GunType::Shotgun(), $bulletDamage, $rate, $bulletSpeed, $reaction, $reloadController, $effectiveRange, $precision, $scheduler);
    }
    /**
     * @return ShotgunBulletType
     */
    public function getBulletType(): ShotgunBulletType {
        return $this->bulletType;
    }

    /**
     * @return int
     */
    public function getPellets(): int {
        return $this->pellets;
    }

    /**
     * @return ShotgunScope
     */
    public function getScope() :ShotgunScope {
        return $this->scope;
    }

    /**
     * @param ShotgunScope $scope
     */
    public function setScope(ShotgunScope $scope): void {
        $this->scope = $scope;
    }
}