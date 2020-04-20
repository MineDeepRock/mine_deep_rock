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
use gun_system\models\ReloadDuration;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

class LightMachineGun extends Gun
{
    private $overheatGauge;
    private $isOverheat;
    private $overheatRate;

    private $onOverheated;
    private $onFinishOverheat;

    public function __construct(OverheatRate $overheatRate, BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, int $bulletCapacity, ReloadDuration $reloadDuration, EffectiveRange $effectiveRange, GunPrecision $precision, TaskScheduler $scheduler) {
        parent::__construct(GunType::LMG(), $bulletDamage, $rate, $bulletSpeed, $bulletCapacity, 0.0, $reloadDuration, $effectiveRange, $precision, $scheduler);
        $this->overheatGauge = new OverheatGauge(function () {
            $this->cancelShooting();
            $this->isOverheat = true;
            ($this->onOverheated)();

            $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $currentTick): void {
                $this->isOverheat = false;
                ($this->onFinishOverheat)();
                $this->overheatGauge->reset();
            }), 20 * 2);

        }, function () {
            $this->isOverheat = false;
        });

        $this->isOverheat = false;
        $this->overheatRate = $overheatRate;

        if ($this->overheatRate->getPerShoot() !== 0) {
            $this->scheduler->scheduleRepeatingTask(new ClosureTask(function (int $currentTick): void {
                $this->overheatGauge->down(34);
            }), 20 * 1);
        }
    }

    public function tryShootingOnce(Closure $onSucceed): bool {
        if ($this->isOverheat)
            return false;

        $func = function () use ($onSucceed) {
            $this->overheatGauge->raise($this->overheatRate);
            $onSucceed($this->scheduler);
        };
        return parent::tryShootingOnce($func);
    }

    public function tryShooting(Closure $onSucceed): bool {
        if ($this->isOverheat)
            return false;

        $func = function () use ($onSucceed) {
            $this->overheatGauge->raise($this->overheatRate);
            $onSucceed($this->scheduler);
        };

        return parent::tryShooting($func);
    }

    /**
     * @param mixed $onFinishOverheat
     */
    public function setOnFinishOverheat($onFinishOverheat): void {
        $this->onFinishOverheat = $onFinishOverheat;
    }

    protected function shootOnce(Closure $onSucceed): void {
        if ($this->overheatRate->getPerShoot() !== 0)
            $this->overheatGauge->raise($this->overheatRate);

        parent::shootOnce($onSucceed);
    }

    protected function shoot(Closure $onSucceed): void {
        if ($this->overheatRate->getPerShoot() !== 0)
            $this->overheatGauge->raise($this->overheatRate);

        parent::shoot($onSucceed);
    }

    /**
     * @param Closure $onOverheated
     */
    public function setOnOverheated(Closure $onOverheated): void {
        $this->onOverheated = $onOverheated;
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
    public function getPerShoot() {
        return $this->perShoot;
    }
}


class OverheatGauge
{
    private $gauge;
    private $onOverheated;
    private $onReset;

    public function __construct(\Closure $onOverheated, \Closure $onReset) {
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