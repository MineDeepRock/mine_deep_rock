<?php


namespace gun_system\models\revolver;


use gun_system\models\assault_rifle\attachiment\scope\AssaultRifleScope;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\GunType;
use gun_system\models\ReloadController;
use gun_system\models\revolver\attachment\IronSightForRevolver;
use pocketmine\scheduler\TaskScheduler;

class Revolver extends Gun
{
    private $scope;

    public function __construct(BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, float $reaction, ReloadController $reloadController, EffectiveRange $effectiveRange, GunPrecision $precision, TaskScheduler $scheduler) {
        $this->setScope(new IronSightForRevolver());

        parent::__construct(GunType::Revolver(), $bulletDamage, $rate, $bulletSpeed, $reaction, $reloadController, $effectiveRange, $precision, $scheduler);
    }

    /**
     * @return IronSightForRevolver
     */
    public function getScope(): IronSightForRevolver {
        return $this->scope;
    }

    /**
     * @param IronSightForRevolver $scope
     */
    public function setScope(IronSightForRevolver $scope): void {
        $this->scope = $scope;
    }
}