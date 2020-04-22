<?php


namespace gun_system\models\attachment\bullet;


class SlugBullet extends ShotgunBullet
{
    public function __construct() { parent::__construct(ShotgunBulletType::Slug()); }
}
