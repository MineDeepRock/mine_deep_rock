<?php


namespace gun_system\models\shotgun\attachment\muzzle;


use gun_system\models\attachment\Muzzle;
use gun_system\models\GunType;

class ShotgunMuzzle extends Muzzle
{
    public function __construct(string $name, float $additionalPrecision, float $additionalDamage) {
        parent::__construct($name, GunType::Shotgun(), $additionalPrecision, $additionalDamage);
    }
}