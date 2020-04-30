<?php


namespace gun_system\models\light_machine_gun;


use Closure;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\GunType;
use gun_system\models\ReloadingType;

class LightMachineGun extends Gun
{
    //TODO:ここじゃない
    private $overheatRate;

    public function __construct(OverheatRate $overheatRate, BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, ReloadingType $reloadingType, EffectiveRange $effectiveRange, GunPrecision $precision) {
        parent::__construct(GunType::LMG(), $bulletDamage, $rate, $bulletSpeed, 0.0, $reloadingType, $effectiveRange, $precision);

        $this->overheatRate = $overheatRate;
    }

    /**
     * @return OverheatRate
     */
    public function getOverheatRate(): OverheatRate {
        return $this->overheatRate;
    }
}

class OverheatRate
{
    private $perShoot;

    public function __construct(int $perShoot) {
        $this->perShoot = $perShoot;
    }

    /**
     * @return int
     */
    public function getPerShoot(): int {
        return $this->perShoot;
    }
}


class OverheatGauge
{
    private $gauge;
    private $onOverheated;
    private $onReset;

    public function __construct(Closure $onOverheated, Closure $onReset) {
        $this->gauge = 0;
        $this->onOverheated = $onOverheated;
        $this->onReset = $onReset;
    }

    public function raise(OverheatRate $value): void {
        $this->gauge += $value->getPerShoot();
        if ($this->gauge >= 100) {
            ($this->onOverheated)();
        }
    }

    public function down(int $value): void {
        $this->gauge -= $value;
        if ($this->gauge < 0)
            $this->gauge = 0;
    }

    public function reset(): void {
        $this->gauge = 0;
    }
}