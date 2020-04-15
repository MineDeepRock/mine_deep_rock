<?php


namespace gun_system\models;


use Closure;
use ValueObject;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

abstract class Gun extends ValueObject
{
    private $type;

    private $bulletPower;
    private $rate;
    private $bulletSpeed;
    private $bulletCapacity;
    private $currentBullet;
    private $reaction;
    private $reloadDuration;
    private $range;
    private $Precision;

    private $lastShootDate;
    private $onReloading;

    //TODO:
    //非同期処理のためにある。なくしたい。
    private $scheduler;

    public function __construct(GunType $type, float $bulletPower, GunRate $rate, BulletSpeed $bulletSpeed, int $bulletCapacity, float $reaction, ReloadDuration $reloadDuration, int $range, GunPrecision $accurate, TaskScheduler $scheduler) {
        $this->type = $type;

        $this->bulletPower = $bulletPower;
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
        $this->Precision = $accurate;
    }

    private function canShoot(): bool {
        $onCoolTime = (microtime(true) - $this->lastShootDate) <= (1 / $this->rate->getPerSecond());
        return !$onCoolTime && !$this->onReloading;
    }

    public function shoot(Closure $onSucceed): ?string {
        if ($this->canShoot()) {
            $this->lastShootDate = microtime(true);
            $this->currentBullet--;
            $onSucceed();
            return "残弾:" . $this->currentBullet;
        }

        return null;
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
            function (int $currentTick) use ($remainingBullet,$onFinished): void {
                //アイテム消費されたら、リロードがはじまる
                if ($this->bulletCapacity > $remainingBullet) {
                    $this->currentBullet = $remainingBullet;
                } else {
                    $this->currentBullet = $this->bulletCapacity;
                }
                $onFinished();
                $this->onReloading = false;
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
        return $this->Precision;
    }

    /**
     * @return float
     */
    public function getBulletPower(): float {
        return $this->bulletPower;
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