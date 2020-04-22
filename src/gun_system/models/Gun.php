<?php


namespace gun_system\models;


use Closure;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

abstract class Gun
{
    private $type;

    protected $bulletDamage;
    private $rate;
    protected $bulletSpeed;
    protected $bulletCapacity;
    protected $currentBullet;
    private $reaction;
    protected $reloadDuration;
    protected $effectiveRange;
    private $precision;

    private $damageCurve;

    protected $onReloading;
    protected $onCoolTime;

    private $isShooting;
    private $whenBecomeReady;

    //TODO:
    //非同期処理のためにある。なくしたい。
    protected $scheduler;
    private $shootingTaskHandler;
    private $delayShootingTaskHandler;

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

        $this->onReloading = false;
        $this->scheduler = $scheduler;
        $this->precision = $precision;
        $this->initDamageCurve();
    }

    private function initDamageCurve(): void {
        $maxDamage = $this->bulletDamage->getMaxDamage();
        $minDamage = $this->bulletDamage->getMinDamage();

        $effectiveRangeStart = $this->effectiveRange->getStart();
        $effectiveRangeEnd = $this->effectiveRange->getEnd();

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

    public function ontoCoolTime(): void {
        $this->onCoolTime = true;
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $currentTick): void {
            if ($this->whenBecomeReady !== null)
                ($this->whenBecomeReady)();

            $this->onCoolTime = false;
        }), 20 * 1 / $this->rate->getPerSecond());
    }

    public function cancelShooting(): void {
        $this->isShooting = false;
        if ($this->shootingTaskHandler !== null)
            $this->shootingTaskHandler->cancel();
        if ($this->delayShootingTaskHandler !== null)
            $this->delayShootingTaskHandler->cancel();
    }

    public function delayShooting(int $second, Closure $onSucceed) {
        $this->delayShootingTaskHandler = $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $currentTick) use ($onSucceed) : void {
            $this->shoot($onSucceed);
        }), 20 * $second);
    }

    public function tryShootingOnce(Closure $onSucceed): Response {
        if ($this->onReloading)
            return new Response(false, "リロード中");
        if ($this->currentBullet === 0)
            return new Response(false, "マガジンに弾がありません");
        if ($this->onCoolTime)
            return new Response(false);

        $this->shootOnce($onSucceed);
        return new Response(true);
    }

    protected function shootOnce(Closure $onSucceed): void {
        $this->ontoCoolTime();
        if (!$this->isShooting) {
            $this->currentBullet--;
            $onSucceed($this->scheduler);
        }
    }

    public function tryShooting(Closure $onSucceed): Response {
        if ($this->onReloading)
            return new Response(false, "リロード中");
        if ($this->currentBullet === 0)
            return new Response(false, "マガジンに弾がありません");
        if ($this->onCoolTime) {
            //TODO: 1/rate - (now-lastShootDate)
            $this->delayShooting(1 / $this->rate->getPerSecond(), $onSucceed);
            return new Response(true);
        }

        $this->shoot($onSucceed);
        return new Response(true);
    }

    protected function shoot(Closure $onSucceed): void {
        $this->isShooting = true;
        if ($this->shootingTaskHandler !== null)
            $this->shootingTaskHandler->cancel();
        if ($this->delayShootingTaskHandler !== null)
            $this->delayShootingTaskHandler->cancel();


        if (GunType::LMG()->equal($this->type)) {
            //発射までにヨッコラショする時間ディレイ！！
            $this->shootingTaskHandler = $this->scheduler->scheduleDelayedRepeatingTask(new ClosureTask(function (int $currentTick) use ($onSucceed): void {
                //TODO:あんまりよくない
                $this->ontoCoolTime();
                $this->currentBullet--;
                $onSucceed($this->scheduler);
                if ($this->currentBullet === 0)
                    $this->cancelShooting();
            }), 20 * 0.5, 20 * (1 / $this->rate->getPerSecond()));
        } else {
            $this->shootingTaskHandler = $this->scheduler->scheduleRepeatingTask(new ClosureTask(function (int $currentTick) use ($onSucceed): void {
                //TODO:あんまりよくない
                $this->ontoCoolTime();
                $this->currentBullet--;
                $onSucceed($this->scheduler);
                if ($this->currentBullet === 0)
                    $this->cancelShooting();
            }), 20 * (1 / $this->rate->getPerSecond()));
        }
    }

    public function tryReload(int $inventoryBullets, Closure $onStarted, Closure $onFinished): Response {
        if ($this->currentBullet === $this->bulletCapacity)
            return new Response(false, $this->currentBullet . "/" . $this->bulletCapacity);

        if ($this->onReloading)
            return new Response(false, "リロード中");

        if ($inventoryBullets === 0)
            return new Response(false, "残弾がありません");

        $this->onReloading = true;
        $consumedBullets = $this->bulletCapacity - $this->currentBullet;
        $onStarted($consumedBullets);
        $this->reload($inventoryBullets, $onFinished);

        return new Response(true);
    }

    protected function reload(int $inventoryBullets, Closure $onFinished): void {
        $this->scheduler->scheduleDelayedTask(new ClosureTask(
            function (int $currentTick) use ($inventoryBullets, $onFinished): void {
                $empty = $this->bulletCapacity - $this->currentBullet;
                if ($empty > $inventoryBullets) {
                    $this->currentBullet += $inventoryBullets;
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
     * @return ReloadDuration
     */
    public function getReloadDuration(): ReloadDuration {
        return $this->reloadDuration;
    }

    /**
     * @param mixed $whenBecomeReady
     */
    public function setWhenBecomeReady($whenBecomeReady): void {
        $this->whenBecomeReady = $whenBecomeReady;
    }
}

class Response
{
    private $isSuccess;
    private $message;

    public function __construct(bool $isSuccess, ?string $message = null) {
        $this->isSuccess = $isSuccess;
        $this->message = $message;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool {
        return $this->isSuccess;
    }


    /**
     * @return string|null
     */
    public function getMessage(): ?string {
        return $this->message;
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