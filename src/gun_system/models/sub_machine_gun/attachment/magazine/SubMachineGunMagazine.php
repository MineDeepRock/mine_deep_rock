<?php


namespace gun_system\models\assault_rifle\attachiment\magazine;


use gun_system\models\attachment\Magazine;
use gun_system\models\GunType;

class SubMachineGunMagazine extends Magazine
{
    public function __construct(string $name, int $additionalBullets, float $reloadSpeed) {
        parent::__construct($name, GunType::SMG(), $additionalBullets, $reloadSpeed);
    }
}