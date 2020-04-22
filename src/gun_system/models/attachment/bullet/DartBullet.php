<?php


namespace gun_system\models\attachment\bullet;


class DartBullet extends ShotgunBullet
{
    public function __construct() { parent::__construct(ShotgunBulletType::Dart()); }
}