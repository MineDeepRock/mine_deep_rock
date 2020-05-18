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
    private $remainingAmmo;
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
        $this->remainingAmmo = $this->reloadingType->initialAmmo;

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

    /**
     * @return int
     */
    public function getRemainingAmmo(): int {
        return $this->remainingAmmo;
    }

    /**
     * @param int $remainingAmmo
     */
    public function setRemainingAmmo(int $remainingAmmo): void {
        $this->remainingAmmo = $remainingAmmo;
    }

    public function getDescribe():string{
        $describe = "";
        $reloadingType = $this->getReloadingType();

        $describe .= "\n" . $this->getType()->getTypeText();
        $describe .= "\n" . $this::NAME;
        $describe .= "\n 火力:" . $this->getBulletDamage()->getValue();
        $describe .= "\n 弾速:" . $this->getBulletSpeed()->getPerSecond();
        $describe .= "\n 毎秒レート:" . $this->getRate()->getPerSecond();
        $describe .= "\n 装弾数:" . $reloadingType->magazineCapacity. "/" .$reloadingType->initialAmmo;
        $describe .= "\n リロード時間:" . $reloadingType->secondToString();
        $describe .= "\n 反動:" . $this->getReaction();
        $describe .= "\n 精度:" . "ADS:" . $this->getPrecision()->getADS() . "腰撃ち:" . $this->getPrecision()->getHipShooting();
        return $describe;
    }
}

class BulletDamage
{
    private $value;

    public function __construct(int $value) {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int {
        return $this->value;
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

    public function __construct(float $perShoot) {
        $this->perShoot = $perShoot;
    }

    /**
     * @return float
     */
    public function getPerShoot(): float {
        return $this->perShoot;
    }
}

abstract class ReloadingType
{
    public $initialAmmo;
    public $magazineCapacity;

    public function __construct(int $initialAmmo, int $magazineCapacity) {
        $this->initialAmmo = $initialAmmo;
        $this->magazineCapacity = $magazineCapacity;
    }

    abstract function secondToString(): string;
}

class MagazineReloadingType extends ReloadingType
{
    public $second;

    public function __construct(int $initialAmmo, int $magazineCapacity, float $second) {
        parent::__construct($initialAmmo, $magazineCapacity);
        $this->second = $second;
    }

    function toString(): string {
        return "装填数:" . $this->magazineCapacity . ", リロード時間:" . $this->second;
    }

    function secondToString(): string {
        return $this->second . "s";
    }
}

class ClipReloadingType extends ReloadingType
{
    public $clipCapacity;
    public $secondOfClip;
    public $secondOfOne;

    public function __construct(int $initialAmmo, int $magazineCapacity, int $clipCapacity, float $secondOfClip, float $secondOfOne) {
        parent::__construct($initialAmmo, $magazineCapacity);
        $this->clipCapacity = $clipCapacity;
        $this->secondOfClip = $secondOfClip;
        $this->secondOfOne = $secondOfOne;
    }

    function secondToString(): string {
        return $this->secondOfOne . "s(" . $this->secondOfClip . "s)";
    }
}

class OneByOneReloadingType extends ReloadingType
{
    public $second;

    public function __construct(int $initialAmmo, int $magazineCapacity, float $second) {
        parent::__construct($initialAmmo, $magazineCapacity);
        $this->second = $second;
    }

    function secondToString(): string {
        return $this->second . "s";
    }
}


