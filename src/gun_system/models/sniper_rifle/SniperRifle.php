<?php


namespace gun_system\models\sniper_rifle;


use Closure;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\GunType;
use gun_system\models\ReloadDuration;
use pocketmine\scheduler\TaskScheduler;

class SniperRifle extends Gun
{
    public function __construct(BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, int $bulletCapacity, float $reaction, ReloadDuration $reloadDuration, EffectiveRange $effectiveRange, GunPrecision $precision, TaskScheduler $scheduler) {
        parent::__construct(GunType::SniperRifle(), $bulletDamage, $rate, $bulletSpeed, $bulletCapacity, $reaction, $reloadDuration, $effectiveRange, $precision, $scheduler);
    }

    public function shoot(Closure $onSucceed): void {
        if ($this->getCurrentBullet() !== 0 && !$this->isReloading()) {
            if (!$this->onCoolTime()) {
                $this->lastShootDate = microtime(true);
                $this->currentBullet--;
                $onSucceed($this->scheduler);
            }
        }
    }
}