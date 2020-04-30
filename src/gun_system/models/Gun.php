<?php


namespace gun_system\models;


abstract class Gun
{
    private $type;

    private $bulletDamage;
    private $rate;
    private $bulletSpeed;
    private $reaction;
    private $effectiveRange;
    private $precision;
    private $damageCurve;
    private $reloadingType;

    public function __construct(GunType $type, BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, float $reaction, ReloadingType $reloadingType, EffectiveRange $effectiveRange, GunPrecision $precision) {
        $this->type = $type;

        $this->bulletDamage = $bulletDamage;
        $this->rate = $rate;
        $this->bulletSpeed = $bulletSpeed;
        $this->reaction = $reaction;
        $this->effectiveRange = $effectiveRange;
        $this->reloadingType = $reloadingType;

        $this->precision = $precision;
        $this->initDamageCurve(
            $this->bulletDamage->getMaxDamage(),
            $this->bulletDamage->getMinDamage(),
            $this->effectiveRange->getStart(),
            $this->effectiveRange->getEnd());
    }

    private function initDamageCurve(int $maxDamage, int $minDamage, int $effectiveRangeStart, int $effectiveRangeEnd): void {
        $result = [];
        $zeroToStart = [];

        //0からeffectiveRangeStart
        if ($effectiveRangeStart === 0) {
            $zeroToStart[] = $minDamage;
        } else {
            $rise = ($maxDamage-$minDamage)/$effectiveRangeStart;
            $zeroToStart = $this->createWave($effectiveRangeStart, $rise, $maxDamage, $minDamage);
        }

        $effectiveStartToEnd = array_fill($effectiveRangeStart, $effectiveRangeEnd - $effectiveRangeStart, $maxDamage);

        if ($effectiveRangeEnd === 100) {
            $decreaseWave = [];
        } else {
            $decreaseWave = $this->createWave(100 - $effectiveRangeEnd, ($minDamage - $maxDamage) / (100 - $effectiveRangeEnd), $maxDamage, $maxDamage);
        }

        //$minDamageWave = createWave(99-count($zeroToStart)-count($effectiveStartToEnd)-count($decreaseWave),0,$minDamage);
        foreach ($zeroToStart as $value) {
            $result[] = $value;
        }
        foreach ($effectiveStartToEnd as $value) {
            $result[] = $value;
        }
        foreach ($decreaseWave as $value) {
            $result[] = $value;
        }
        //foreach($minDamageWave as $value){
        //  $result[] = $value;
        //}

        $this->damageCurve = $result;
    }

    private function createWave(int $length, float $rate, int $max, float $initValue = 0): array {
        $value = $initValue;
        $wave = [];
        $i = 0;
        while ($i < $length) {
            $wave[] = $value > $max ? $max : $value;
            $value += $rate;
            $i++;
        }
        return $wave;
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
     * @return array
     */
    public function getDamageCurve(): array {
        return $this->damageCurve;
    }

    /**
     * @return BulletDamage
     */
    public function getBulletDamage(): BulletDamage {
        return $this->bulletDamage;
    }

    /**
     * @return EffectiveRange
     */
    public function getEffectiveRange(): EffectiveRange {
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
}

class EffectiveRange
{
    private $start;
    private $end;

    public function __construct(int $start, int $end) {
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * @return int
     */
    public function getStart(): int {
        return $this->start;
    }

    /**
     * @return int
     */
    public function getEnd(): int {
        return $this->end;
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


