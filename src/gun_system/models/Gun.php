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
    protected $currentBullet;
    private $reaction;
    private $reloadDuration;
    private $effectiveRange;
    private $precision;

    private $damageCurve;

    protected $lastShootDate;
    private $onReloading;

    //TODO:
    //非同期処理のためにある。なくしたい。
    protected $scheduler;
    private $shootingTaskHandler;

    public function __construct(GunType $type, BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, int $bulletCapacity, float $reaction, ReloadDuration $reloadDuration, EffectiveRange $effectiveRange, GunPrecision $precision, TaskScheduler $scheduler) {
        $this->type = $type;

        $this->bulletDamage = $bulletDamage;
        $this->rate = $rate;
        $this->bulletSpeed = $bulletSpeed;
        $this->bulletCapacity = $bulletCapacity;
        $this->currentBullet = $bulletCapacity;
        $this->reaction = $reaction;
        $this->reloadDuration = $reloadDuration;
        $this->effectiveRange = $effectiveRange;

        $this->lastShootDate = microtime(true);
        $this->onReloading = false;
        $this->scheduler = $scheduler;
        $this->precision = $precision;

        $maxDamage = $this->bulletDamage->getMaxDamage();
        $minDamage = $this->bulletDamage->getMinDamage();

        $effectiveRangeStart = $effectiveRange->getStart();
        $effectiveRangeEnd = $effectiveRange->getEnd();

        $maxDamageRange = array_fill(0, $effectiveRangeEnd, $maxDamage);
        for ($i = 0; $i <= $maxDamage - $minDamage; ++$i)
            array_push($maxDamageRange, $maxDamage - $i);
        $minDamageRange = array_fill($effectiveRangeEnd + ($maxDamage - $minDamage), 100 - ($effectiveRangeEnd + ($maxDamage - $minDamage)), $minDamage);

        $range = $maxDamageRange + $minDamageRange;

        if ($effectiveRangeStart !== 0) {
            foreach ($range as $key => $value) {
                if ($key < $effectiveRangeStart) {
                    $this->damageCurve[$key] = $minDamage;
                } else {
                    $this->damageCurve[$key] = $value;
                }
            }
        } else {
            $this->damageCurve = $range;
        }
    }

    public function isReloading(): bool {
        return $this->onReloading;
    }

    protected function onCoolTime(): bool {
        return (microtime(true) - $this->lastShootDate) <= (1 / $this->rate->getPerSecond());
    }

    public function cancelShooting(): void {
        if ($this->shootingTaskHandler !== null)
            $this->shootingTaskHandler->cancel();
    }

    public function tryShootingOnce(Closure $onSucceed): bool {
        if ($this->currentBullet === 0)
            return false;
        if ($this->onReloading)
            return false;
        if ($this->onCoolTime())
            return false;
        $this->shootOnce($onSucceed);
        return true;
    }

    public function tryShooting(Closure $onSucceed): bool {
        if ($this->currentBullet === 0)
            return false;
        if ($this->onReloading)
            return false;
        $this->shoot($onSucceed);
        return true;
    }

    protected function shootOnce(Closure $onSucceed):void{
        $this->lastShootDate = microtime(true);
        $this->currentBullet--;
        $onSucceed($this->scheduler);
    }

    protected function shoot(Closure $onSucceed): void {
        if ($this->onCoolTime()) {
            $this->shootingTaskHandler = $this->scheduler->scheduleDelayedRepeatingTask(new ClosureTask(function (int $currentTick) use ($onSucceed): void {
                $this->lastShootDate = microtime(true);
                $this->currentBullet--;
                $onSucceed($this->scheduler);
                if ($this->currentBullet == 0)
                    $this->cancelShooting();
            }), 20 * ((1 / $this->rate->getPerSecond()) - (microtime(true) - $this->lastShootDate)), 20 * (1 / $this->rate->getPerSecond()));
        } else {
            $this->shootingTaskHandler = $this->scheduler->scheduleRepeatingTask(new ClosureTask(function (int $currentTick) use ($onSucceed): void {
                $this->lastShootDate = microtime(true);
                $this->currentBullet--;
                $onSucceed($this->scheduler);
                if ($this->currentBullet == 0)
                    $this->cancelShooting();
            }), 20 * (1 / $this->rate->getPerSecond()));
            //TODO:ここDelay入れれば持ち替え時間実装できる
        }
    }

    public function reload(int $remainingBullet, Closure $onStarted, Closure $onFinished): void {
        //アイテム消費が先
        $onStarted($this->bulletCapacity - $this->currentBullet);

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

    /**
     * @return array
     */
    public function getDamageCurve(): array {
        return $this->damageCurve;
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