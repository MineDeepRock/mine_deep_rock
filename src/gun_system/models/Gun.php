<?php


namespace gun_system\models;


use Closure;
use Entity;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\TaskScheduler;

abstract class Gun extends Entity
{
    private $damage;
    private $rate;
    private $bulletSpeed;
    private $bulletCapacity;
    private $currentBullet;
    private $reaction;
    private $reloadDuration;
    private $range;

    private $lastShootDate;
    private $onReloading;

    //TODO:
    //非同期処理のためにある。なくしたい。
    private $scheduler;

    public function __construct(float $damage, GunRate $rate,BulletSpeed $bulletSpeed, int $bulletCapacity, float $reaction, ReloadDuration $reloadDuration, int $range, TaskScheduler $scheduler) {
        $this->damage = $damage;
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
    }

    public function canShoot(): bool {
        $onCoolTime = (microtime(true) - $this->lastShootDate) <= $this->rate->getPerSecond();
        return !$onCoolTime && !$this->onReloading;
    }

    public function shoot(Closure $onSucceed): ?string {
        if ($this->currentBullet === 0) {
            $this->reload();
            return "リロード";

        } else if ($this->canShoot()) {
            $this->lastShootDate = microtime(true);
            $this->currentBullet--;
            $onSucceed();
            return "残弾:" . $this->currentBullet;

        }

        return null;
    }

    public function reload(): void {
        $this->onReloading = true;
        $run = function () { $this->onReloading = false; };
        $this->scheduler->scheduleDelayedTask(new ClosureTask(
            function (int $currentTick) use ($run): void {
                ($run)();
            }
        ), 20 * $this->reloadDuration->getSecond());
        $this->currentBullet = $this->bulletCapacity;
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
}

class BulletSpeed
{
    private $value;

    public function __construct(float $value) {

        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
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