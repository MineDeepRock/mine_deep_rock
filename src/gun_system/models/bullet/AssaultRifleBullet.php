<?php


namespace gun_system\models\bullet;



use gun_system\models\bullet\Bullet;
use gun_system\models\GunType;

class AssaultRifleBullet extends Bullet
{
    public function __construct() { parent::__construct(GunType::AssaultRifle()); }
}