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
    private $onOverheat;
    private $overheatRate;

    public function __construct(OverheatRate $overheatRate, BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, int $bulletCapacity, ReloadDuration $reloadDuration, EffectiveRange $effectiveRange, GunPrecision $precision, TaskScheduler $scheduler) {
        parent::__construct(GunType::LMG(), $bulletDamage, $rate, $bulletSpeed, $bulletCapacity, 0.0, $reloadDuration, $effectiveRange, $precision, $scheduler);

        $this->overheatGauge = new OverheatGauge(function () {
            $this->cancelShooting();
            $this->onOverheat = true;

            $this->scheduler->scheduleRepeatingTask(new ClosureTask(function (int $currentTick): void {
                $this->onOverheat = false;
            }), 20 * 2);
        }, function () {
            $this->onOverheat = false;
        });

        $this->onOverheat = false;
        $this->overheatRate = $overheatRate;

        if ($this->overheatRate->getPerShoot() !== 0) {
            $this->scheduler->scheduleRepeatingTask(new ClosureTask(function (int $currentTick): void {
                $this->overheatGauge->down($this->overheatRate);
            }), 20 * 1);
        }
    }

    /**
     * @return OverheatGauge
     */
    public function getOverheatGauge(): OverheatGauge {
        return $this->overheatGauge;
    }

    public function doCoolDown(): void {
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $currentTick): void {
            $this->overheatGauge->reset();
        }), 20 * 1);
    }

    public function shootOnce(Closure $onSucceed) {
        if ($this->overheatRate->getPerShoot() !== 0)
            $this->overheatGauge->raise($this->overheatRate);
        parent::shootOnce($onSucceed);
    }

    public function shoot(Closure $onSucceed): void {
        if ($this->overheatRate->getPerShoot() !== 0)
            $this->overheatGauge->raise($this->overheatRate);
        parent::shoot($onSucceed);
    }

    /**
     * @return bool
     */
    public function onOverheat(): bool {
        return $this->onOverheat;
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

    public function down(OverheatRate $value): void {
        if ($this->gauge !== 0)
            $this->gauge -= $value->getPerShoot();
    }

    public function reset(): void {
        $this->gauge = 0;
    }
}