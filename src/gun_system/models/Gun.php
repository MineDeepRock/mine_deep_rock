<?php


namespace gun_system\models;


abstract class Gun
{
    private $type;

    const NAME = "";

    private $bulletDamage;
    private $rate;
    private $bulletSpeed;
    private $reaction;
    private $effectiveRange;
    private $precision;
    private $reloadingType;
    private $overheatRate;

    private $moneyCost;
    private $killCountCondition;

    public function __construct(GunType $type, BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, float $reaction, ReloadingType $reloadingType, array $effectiveRange, GunPrecision $precision, OverheatRate $overheatRate, MoneyCost $moneyCost = null, KillCountCondition $killCountCondition = null) {
        $this->type = $type;

        $this->bulletDamage = $bulletDamage;
        $this->rate = $rate;
        $this->bulletSpeed = $bulletSpeed;
        $this->reaction = $reaction;
        $this->effectiveRange = $effectiveRange;
        $this->reloadingType = $reloadingType;

        $this->precision = $precision;
        $this->effectiveRange = $effectiveRange;
        $this->overheatRate = $overheatRate;

        $this->moneyCost = $moneyCost ?? new MoneyCost(1000);
        $this->killCountCondition = $killCountCondition;
    }

    /**
     * @return BulletSpeed
     */
    public function getBulletSpeed(): BulletSpeed {
        return $this->bulletSpeed;
    }

    /**
     * @return float
     */
    public function getReaction(): float {
        return $this->reaction;
    }

    /**
     * @return mixed
     */
    public function getPrecision(): GunPrecision {
        return $this->precision;
    }

    /**
     * @return GunType
     */
    public function getType(): GunType {
        return $this->type;
    }

    /**
     * @return BulletDamage
     */
    public function getBulletDamage(): BulletDamage {
        return $this->bulletDamage;
    }

    /**
     * @return array
     */
    public function getEffectiveRange(): array {
        return $this->effectiveRange;
    }

    /**
     * @return GunRate
     */
    public function getRate(): GunRate {
        return $this->rate;
    }

    /**
     * @param GunPrecision $precision
     */
    public function setPrecision(GunPrecision $precision): void {
        $this->precision = $precision;
    }

    /**
     * @return ReloadingType
     */
    public function getReloadingType(): ReloadingType {
        return $this->reloadingType;
    }

    /**
     * @return OverheatRate
     */
    public function getOverheatRate(): OverheatRate {
        return $this->overheatRate;
    }

    /**
     * @return MoneyCost
     */
    public function getMoneyCost(): MoneyCost {
        return $this->moneyCost;
    }

    /**
     * @return KillCountCondition|null
     */
    public function getKillCountCondition(): ?KillCountCondition {
        return $this->killCountCondition;
    }
}

class BulletDamage
{
    private $maxDamage;
    private $minDamage;

    public function __construct(int $maxDamage, int $minDamage) {
        $this->maxDamage = $maxDamage;
        $this->minDamage = $minDamage;
    }

    /**
     * @return int
     */
    public function getMaxDamage(): int {
        return $this->maxDamage;
    }

    /**
     * @return int
     */
    public function getMinDamage(): int {
        return $this->minDamage;
    }
}

class GunPrecision
{
    private $percentADS;
    private $percentHipShooting;

    public function __construct(float $percentADS, float $percentHipShooting) {
        $this->percentADS = $percentADS;
        $this->percentHipShooting = $percentHipShooting;
    }

    /**
     * @return float
     */
    public function getADS(): float {
        return $this->percentADS;
    }

    /**
     * @return float
     */
    public function getHipShooting(): float {
        return $this->percentHipShooting;
    }

}


class BulletSpeed
{
    private $perSecond;

    public function __construct(float $perSecond) {

        $this->perSecond = $perSecond;
    }

    /**
     * @return mixed
     */
    public function getPerSecond() {
        return $this->perSecond;
    }

}

class GunRate
{
    private $perSecond;

    public function __construct(float $perSecond) {
        $this->perSecond = $perSecond;
    }

    /**
     * @return float
     */
    public function getPerSecond(): float {
        return $this->perSecond;
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

abstract class ReloadingType
{
    public $magazineCapacity;

    public function __construct(int $magazineCapacity) {
        $this->magazineCapacity = $magazineCapacity;
    }

    abstract function toString(): string;
}

class MagazineReloadingType extends ReloadingType
{
    public $second;

    public function __construct(int $magazineCapacity, float $second) {
        parent::__construct($magazineCapacity);
        $this->second = $second;
    }

    function toString(): string {
        return strval($this->second);
    }
}

class ClipReloadingType extends ReloadingType
{
    public $clipCapacity;
    public $secondOfClip;
    public $secondOfOne;

    public function __construct(int $magazineCapacity, int $clipCapacity, float $secondOfClip, float $secondOfOne) {
        parent::__construct($magazineCapacity);
        $this->clipCapacity = $clipCapacity;
        $this->secondOfClip = $secondOfClip;
        $this->secondOfOne = $secondOfOne;
    }

    function toString(): string {
        return " クリップ:" . "(" . $this->clipCapacity . ")" . $this->secondOfClip . ", 1発:" . $this->secondOfOne;
    }

}

class OneByOneReloadingType extends ReloadingType
{
    public $second;

    public function __construct(int $magazineCapacity, float $second) {
        parent::__construct($magazineCapacity);
        $this->second = $second;
    }

    function toString(): string {
        return strval($this->second);
    }
}


