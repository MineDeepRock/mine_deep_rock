<?php


namespace gun_system\models;


use Closure;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;
use pocketmine\scheduler\TaskScheduler;

abstract class Gun
{
    private $type;

    private $bulletDamage;
    private $rate;
    private $bulletSpeed;
    private $bulletCapacity;
    private $currentBullet;
    private $reaction;
    private $reloadDuration;
    private $range;
    private $precision;

    private $lastShootDate;
    private $onReloading;

    //TODO:
    //非同期処理のためにある。なくしたい。
    private $scheduler;
    private $shootingTaskHandler;

    public function __construct(GunType $type, float $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, int $bulletCapacity, float $reaction, ReloadDuration $reloadDuration, int $range, GunPrecision $precision, TaskScheduler $scheduler) {
        $this->type = $type;

        $this->bulletDamage = $bulletDamage;
        $this->rate = $rate;
        $this->bulletSpeed = $bulletSpeed;
        $this->bulletCapacity = $bulletCapacity;
        $this->currentBullet = $bulletCapacity;
        $this->reaction = $reaction;
        $this->reloadDuration = $reloadDuration;
        $this->range = $range;

        $this->lastShootDate = microtime(true);
        $this->onReloading = false;
        $this->scheduler = $scheduler;
        $this->precision = $precision;
    }

    public function isReloading(): bool {
        return $this->onReloading;
    }

    private function onCoolTime(): bool {
        return (microtime(true) - $this->lastShootDate) <= (1 / $this->rate->getPerSecond());
    }

    public function cancelShooting(): void {
        if ($this->shootingTaskHandler !== null)
            $this->shootingTaskHandler->cancel();
    }

    public function shoot(Closure $onSucceed): void {
        if ($this->currentBullet !== 0 && !$this->onReloading) {
            if ($this->onCoolTime()) {
                $this->shootingTaskHandler = $this->scheduler->scheduleDelayedRepeatingTask(new ClosureTask(function (int $currentTick) use ($onSucceed): void {
                    $this->lastShootDate = microtime(true);
                    $this->currentBullet--;
                    $onSucceed();
                    if ($this->currentBullet == 0)
                        $this->cancelShooting();
                }), 20 * ((1 / $this->rate->getPerSecond()) - (microtime(true) - $this->lastShootDate)), 20 * (1 / $this->rate->getPerSecond()));

            } else {
                $this->lastShootDate = microtime(true);
                $this->currentBullet--;
                $onSucceed();

                if ($this->currentBullet !== 0) {
                    $this->shootingTaskHandler = $this->scheduler->scheduleDelayedRepeatingTask(new ClosureTask(function (int $currentTick) use ($onSucceed): void {
                        $this->lastShootDate = microtime(true);
                        $this->currentBullet--;
                        $onSucceed();
                        if ($this->currentBullet == 0)
                            $this->cancelShooting();
                    }), 20 * (1 / $this->rate->getPerSecond()), 20 * (1 / $this->rate->getPerSecond()));
                }

            }
        }
    }

    public function reload(int $remainingBullet, Closure $onStarted, Closure $onFinished): void {
        //アイテム消費が先
        if ($this->bulletCapacity > $remainingBullet) {
            $onStarted($this->currentBullet = $this->bulletCapacity);
        } else {
            $onStarted($this->currentBullet = $this->bulletCapacity);
        }


        $this->onReloading = true;
        $this->scheduler->scheduleDelayedTask(new ClosureTask(
            function (int $currentTick) use ($remainingBullet, $onFinished): void {
                //アイテム消費されたら、リロードがはじまる
                if ($this->bulletCapacity > $remainingBullet) {
                    $this->currentBullet = $remainingBullet;
                } else {
                    $this->currentBullet = $this->bulletCapacity;
                }
                $this->onReloading = false;
                $onFinished();
            }
        ), 20 * $this->reloadDuration->getSecond());
    }

    /**
     * @return int
     */
    public function getCurrentBullet(): int {
        return $this->currentBullet;
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
     * @return float
     */
    public function getBulletDamage(): float {
        return $this->bulletDamage;
    }

    /**
     * @return int
     */
    public function getRange(): int {
        return $this->range;
    }

    /**
     * @return int
     */
    public function getBulletCapacity(): int {
        return $this->bulletCapacity;
    }

    /**
     * @return GunType
     */
    public function getType(): GunType {
        return $this->type;
    }
}

class GunPrecision
{
    private $percent;

    public function __construct(float $percent) {

        $this->percent = $percent;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->percent;
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

class ReloadDuration
{
    private $second;

    public function __construct(float $second) {
        $this->second = $second;
    }

    /**
     * @return float
     */
    public function getSecond(): float {
        return $this->second;
    }
}