<?php


namespace gun_system\models\bullet;


use gun_system\models\GunType;

class LightMachineGunBullet extends Bullet
{
    public function __construct() { parent::__construct(GunType::LMG()); }
}