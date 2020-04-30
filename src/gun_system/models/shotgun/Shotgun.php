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
use gun_system\models\ReloadingType;

abstract class Shotgun extends Gun
{
    private $bulletType;
    private $pellets;

    public function __construct(ShotgunBulletType $bulletType,int $pellets, BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, float $reaction, ReloadingType $reloadingType, EffectiveRange $effectiveRange, GunPrecision $precision) {
        $this->bulletType = $bulletType;
        $this->pellets = $pellets;

        if ($this->bulletType->equal(ShotgunBulletType::Slug())) {
            $bulletDamage = new BulletDamage($bulletDamage->getMaxDamage() * $this->pellets,$bulletDamage->getMinDamage() * $this->pellets);
            $effectiveRange = new EffectiveRange($effectiveRange->getStart(),$effectiveRange->getEnd()+10);
            $this->pellets = 1;
        }

        parent::__construct(GunType::Shotgun(), $bulletDamage, $rate, $bulletSpeed, $reaction, $reloadingType, $effectiveRange, $precision);
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
}