<?php


namespace gun_system\models\attachment\bullet;


class BuckShotBullet extends ShotgunBullet
{
    public function __construct() { parent::__construct(ShotgunBulletType::Buckshot()); }
}