<?php


namespace gun_system\models\assault_rifle\attachiment\barrel;


use gun_system\models\GunType;

class ArMuzzleBrake extends AssaultRifleBarrel
{
    public function __construct() {
        parent::__construct("ArMuzzleBrake", 100, 100, 87.5, 90);
    }
}