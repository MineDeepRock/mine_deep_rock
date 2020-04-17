<?php


namespace gun_system\models\bullet;


use gun_system\models\GunType;

class SniperRifleBullet extends Bullet
{
    public function __construct() { parent::__construct(GunType::SniperRifle()); }
}