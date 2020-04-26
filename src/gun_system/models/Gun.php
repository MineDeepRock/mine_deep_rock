<?php


namespace gun_system\models;


use Closure;
use gun_system\pmmp\GunSounds;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

abstract class Gun
{
    private $type;

    protected $bulletDamage;
    private $rate;
    protected $bulletSpeed;
    private $reaction;
    protected $reloadController;
    protected $effectiveRange;
    protected $precision;

    private $damageCurve;

    protected $onCoolTime;
    private $isShooting;
    private $whenBecomeReady;

    //非同期処理のためにある。
    protected $scheduler;
    protected $shootingTaskHandler;
    protected $delayShootingTaskHandler;

    public function __construct(GunType $type, BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, float $reaction, ReloadController $reloadController, EffectiveRange $effectiveRange, GunPrecision $precision, TaskScheduler $scheduler) {
        $this->type = $type;

        $this->bulletDamage = $bulletDamage;
        $this->rate = $rate;
        $this->bulletSpeed = $bulletSpeed;
        $this->reaction = $reaction;
        $this->reloadController = $reloadController;
        $this->effectiveRange = $effectiveRange;

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

    public function scare(): void {
        $this->precision = new GunPrecision($this->precision->getADS()-10,$this->precision->getHipShooting()-10);
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function(){
            $this->precision = new GunPrecision($this->precision->getADS()+10,$this->precision->getHipShooting()+10);
        }), 20 * 3);
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

    public function delayShooting(int $second, Closure $onSucceed, bool $isADS) {
        $this->delayShootingTaskHandler = $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $currentTick) use ($onSucceed, $isADS) : void {
            $this->shoot($onSucceed, $isADS);
        }), 20 * $second);
    }

    public function tryShootingOnce(Closure $onSucceed): Response {
        if (!($this->reloadController instanceof MagazineReloadController))
            $this->reloadController->cancelReloading();

        if ($this->reloadController->isReloading())
            return new Response(false, "リロード中");
        if ($this->reloadController->currentBullet === 0)
            return new Response(false, "マガジンに弾がありません");
        if ($this->onCoolTime)
            return new Response(true, $this->getCurrentBullet() . "\\" . $this->getMagazineCapacity());

        $this->shootOnce($onSucceed);
        return new Response(true);
    }

    protected function shootOnce(Closure $onSucceed): void {
        $this->ontoCoolTime();
        if (!$this->isShooting) {
            $this->reloadController->currentBullet--;
            $onSucceed($this->scheduler);
        }
    }

    public function tryShooting(Closure $onSucceed, bool $isADS): Response {
        if (!($this->reloadController instanceof MagazineReloadController))
            $this->reloadController->cancelReloading();

        if ($this->reloadController->isReloading())
            return new Response(false, "リロード中");
        if ($this->reloadController->currentBullet === 0)
            return new Response(false, "マガジンに弾がありません");
        if ($this->onCoolTime) {
            //TODO: 1/rate - (now-lastShootDate)
            $this->delayShooting(1 / $this->rate->getPerSecond(), $onSucceed, $isADS);
            return new Response(true, $this->getCurrentBullet() . "\\" . $this->getMagazineCapacity());
        }

        $this->shoot($onSucceed, $isADS);
        return new Response(true);
    }

    protected function shoot(Closure $onSucceed, bool $isADS): void {
        $this->isShooting = true;
        if ($this->shootingTaskHandler !== null)
            $this->shootingTaskHandler->cancel();
        if ($this->delayShootingTaskHandler !== null)
            $this->delayShootingTaskHandler->cancel();

        if ($this->type->equal(GunType::LMG()) && !$isADS) {
            $this->shootingTaskHandler = $this->scheduler->scheduleDelayedRepeatingTask(new ClosureTask(function (int $currentTick) use ($onSucceed): void {
                //TODO:あんまりよくない
                $this->ontoCoolTime();
                $this->reloadController->currentBullet--;
                $onSucceed($this->scheduler);
                if ($this->reloadController->currentBullet === 0)
                    $this->cancelShooting();
            }), 20 * 0.5, 20 * (1 / $this->rate->getPerSecond()));
        } else {
            $this->shootingTaskHandler = $this->scheduler->scheduleRepeatingTask(new ClosureTask(function (int $currentTick) use ($onSucceed): void {
                //TODO:あんまりよくない
                $this->ontoCoolTime();
                $this->reloadController->currentBullet--;
                $onSucceed($this->scheduler);
                if ($this->reloadController->currentBullet === 0)
                    $this->cancelShooting();
            }), 20 * (1 / $this->rate->getPerSecond()));
        }
    }

    public function cancelReloading(): void {
        if ($this->reloadController instanceof ClipReloadController || $this->reloadController instanceof OneByOneReloadController) {
            $this->reloadController->cancelReloading();
        }
    }

    public function tryReload(Player $owner, int $inventoryBullets, Closure $onStarted, Closure $onFinished): Response {
        if ($this->reloadController->currentBullet === $this->reloadController->magazineCapacity)
            return new Response(false, $this->reloadController->currentBullet . "/" . $this->reloadController->magazineCapacity);

        if ($this->reloadController->isReloading())
            return new Response(false, "リロード中");

        if ($inventoryBullets === 0)
            return new Response(false, "残弾がありません");

        $this->reload($owner, $inventoryBullets, $onStarted, $onFinished);

        return new Response(true);
    }

    protected function reload(Player $owner, int $inventoryBullets, Closure $onStarted, Closure $onFinished): void {
        $this->reloadController->carryOut($owner, $this->scheduler, $inventoryBullets, $onStarted, $onFinished);
    }

    /**
     * @return int
     */
    public function getCurrentBullet(): int {
        return $this->reloadController->currentBullet;
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
    public function getMagazineCapacity(): int {
        return $this->reloadController->magazineCapacity;
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
     * @param mixed $whenBecomeReady
     */
    public function setWhenBecomeReady($whenBecomeReady): void {
        $this->whenBecomeReady = $whenBecomeReady;
    }

    /**
     * @return ReloadController
     */
    public function getReloadController(): ReloadController {
        return $this->reloadController;
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

abstract class ReloadController
{
    public $magazineCapacity;
    public $currentBullet;
    protected $onReloading;

    public function __construct(int $magazineCapacity) {
        $this->onReloading = false;
        $this->magazineCapacity = $magazineCapacity;
        $this->currentBullet = $magazineCapacity;
    }

    abstract function carryOut(Player $owner, TaskScheduler $scheduler, int $inventoryBullets, Closure $onStarted, Closure $onFinished): void;

    abstract function toString(): string;

    /**
     * @return bool
     */
    public function isReloading(): bool {
        return $this->onReloading;
    }
}

class MagazineReloadController extends ReloadController
{
    private $second;

    public function __construct(int $magazineCapacity, float $second) {
        parent::__construct($magazineCapacity);
        $this->second = $second;
    }

    function carryOut(Player $owner, TaskScheduler $scheduler, int $inventoryBullets, Closure $onStarted, Closure $onFinished): void {
        $this->onReloading = true;
        GunSounds::play($owner, GunSounds::MagazineOut());

        $scheduler->scheduleDelayedTask(new ClosureTask(
            function (int $currentTick) use ($owner, $inventoryBullets, $onStarted, $onFinished): void {
                $empty = $this->magazineCapacity - $this->currentBullet;
                if ($empty > $inventoryBullets) {
                    $this->currentBullet += $inventoryBullets;
                    $onStarted($inventoryBullets);
                } else {
                    $this->currentBullet = $this->magazineCapacity;
                    $onStarted($empty);
                }
                GunSounds::play($owner, GunSounds::MagazineIn());
                $this->onReloading = false;
                $onFinished();
            }
        ), 20 * $this->second);
    }


    function toString(): string {
        return strval($this->second);
    }
}

class ClipReloadController extends ReloadController
{
    private $clipCapacity;
    private $secondOfClip;
    private $secondOfOne;
    private $clipReloadTaskHandler;
    private $oneReloadTaskHandler;

    public function __construct(int $magazineCapacity, int $clipCapacity, float $secondOfClip, float $secondOfOne) {
        parent::__construct($magazineCapacity);
        $this->clipCapacity = $clipCapacity;
        $this->secondOfClip = $secondOfClip;
        $this->secondOfOne = $secondOfOne;
    }

    public function cancelReloading(): void {
        if ($this->clipReloadTaskHandler !== null)
            $this->clipReloadTaskHandler->cancel();
        if ($this->oneReloadTaskHandler !== null)
            $this->oneReloadTaskHandler->cancel();
        $this->onReloading = false;
    }

    function carryOut(Player $owner, TaskScheduler $scheduler, int $inventoryBullets, Closure $onStarted, Closure $onFinished): void {
        $emptySlot = $this->magazineCapacity - $this->currentBullet;
        $this->onReloading = true;

        if ($inventoryBullets >= $this->clipCapacity && $emptySlot >= $this->clipCapacity) {
            GunSounds::play($owner, GunSounds::ClipPush());
            $inventoryBullets = $onStarted($this->clipCapacity);

            $this->clipReloadTaskHandler = $scheduler->scheduleDelayedRepeatingTask(new ClosureTask(function (int $currentTick) use ($owner, $scheduler, $inventoryBullets, $onStarted, $onFinished): void {
                $this->currentBullet += $this->clipCapacity;
                $onFinished(true);
                GunSounds::play($owner, GunSounds::ClipPing());
                if ($inventoryBullets < $this->clipCapacity) {
                    $this->clipReloadTaskHandler->cancel();
                    $this->reloadOneByOne($owner, $scheduler, $inventoryBullets, $onStarted, $onFinished);
                }
                if (($this->magazineCapacity - $this->currentBullet) < $this->clipCapacity) {
                    $this->clipReloadTaskHandler->cancel();
                    $this->reloadOneByOne($owner, $scheduler, $inventoryBullets, $onStarted, $onFinished);
                }
            }), 20 * $this->secondOfClip, 20 * $this->secondOfClip);
        } else {
            $this->reloadOneByOne($owner, $scheduler, $inventoryBullets, $onStarted, $onFinished);
        }

    }

    private function reloadOneByOne(Player $owner, TaskScheduler $scheduler, int $inventoryBullets, Closure $onStarted, Closure $onFinished) {
        if ($this->currentBullet !== $this->magazineCapacity) {
            $this->oneReloadTaskHandler = $scheduler->scheduleDelayedRepeatingTask(new ClosureTask(function (int $currentTick) use ($owner, $inventoryBullets, $onStarted, $onFinished): void {
                GunSounds::play($owner, GunSounds::ReloadOne());
                $this->currentBullet++;
                $inventoryBullets = $onStarted(1);
                $onFinished(false);
                if ($inventoryBullets === 0)
                    $this->oneReloadTaskHandler->cancel();
                if ($this->currentBullet === $this->magazineCapacity)
                    $this->oneReloadTaskHandler->cancel();
            }), 20 * $this->secondOfOne, 20 * $this->secondOfOne);
        }
    }

    function toString(): string {
        return " クリップ:" . "(" . $this->clipCapacity . ")" . $this->secondOfClip . ", 1発:" . $this->secondOfOne;
    }
}

class OneByOneReloadController extends ReloadController
{
    private $second;
    private $oneReloadTaskHandler;

    public function __construct(int $magazineCapacity, float $second) {
        parent::__construct($magazineCapacity);
        $this->second = $second;
    }

    public function cancelReloading(): void {
        if ($this->oneReloadTaskHandler !== null)
            $this->oneReloadTaskHandler->cancel();
        $this->onReloading = false;
    }

    function carryOut(Player $owner, TaskScheduler $scheduler, int $inventoryBullets, Closure $onStarted, Closure $onFinished): void {
        $this->onReloading = true;

        $this->oneReloadTaskHandler = $scheduler->scheduleDelayedRepeatingTask(new ClosureTask(function (int $currentTick) use ($owner, $inventoryBullets, $onStarted, $onFinished): void {
            GunSounds::play($owner, GunSounds::ReloadOne());
            $this->currentBullet++;
            $inventoryBullets = $onStarted(1);
            $onFinished(false);
            if ($inventoryBullets === 0)
                $this->oneReloadTaskHandler->cancel();
            if ($this->currentBullet === $this->magazineCapacity)
                $this->oneReloadTaskHandler->cancel();
        }), 20 * $this->second,20 * $this->second);
    }

    function toString(): string {
        return strval($this->second);
    }
}

