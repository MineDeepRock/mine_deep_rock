<?php


namespace gun_system\models;


use Entity;

abstract class Gun extends Entity
{
    private $damage;
    private $rate;
    private $bulletCapacity;
    private $currentBullet;
    private $reaction;
    private $reloadDuration;
    private $range;

    private $onShoot;
    private $onReload;

    public function __construct(float $damage, GunRate $rate, int $bulletCapacity, float $reaction, ReloadDuration $reloadDuration, int $range) {
        $this->damage = $damage;
        $this->rate = $rate;
        $this->bulletCapacity = $bulletCapacity;
        $this->currentBullet = $bulletCapacity;
        $this->reaction = $reaction;
        $this->reloadDuration = $reloadDuration;
        $this->range = $range;
    }

    public function shoot() {
        $this->currentBullet--;

        ($this->onShoot)();
    }

    public function reload() {
        $this->currentBullet = $this->bulletCapacity;

        ($this->onReload)();
    }

    public function listenOnShoot($function) {
        $this->onShoot = $function;
    }

    public function listenOnReload($function) {
        $this->onReload = $function;

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