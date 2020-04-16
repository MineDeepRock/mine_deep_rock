<?php


namespace gun_system\models\shotgun;


use Closure;
use gun_system\models\BulletSpeed;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\GunType;
use gun_system\models\ReloadDuration;
use pocketmine\scheduler\TaskScheduler;

abstract class Shotgun extends Gun
{
    private $pellets;

    public function __construct(int $pellets, float $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, int $bulletCapacity, float $reaction, ReloadDuration $reloadDuration, int $range, GunPrecision $precision, TaskScheduler $scheduler) {
        $this->pellets = $pellets;
        parent::__construct(GunType::Shotgun(),$bulletDamage, $rate, $bulletSpeed, $bulletCapacity, $reaction, $reloadDuration, $range, $precision, $scheduler);
    }

    /**
     * @return int
     */
    public function getPellets(): int {
        return $this->pellets;
    }
}