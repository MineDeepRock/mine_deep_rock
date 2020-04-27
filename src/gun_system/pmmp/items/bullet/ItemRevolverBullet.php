<?php


namespace gun_system\pmmp\items\bullet;


use gun_system\models\attachment\bullet\Bullet;
use gun_system\models\attachment\bullet\RevolverBullet;
use gun_system\models\BulletId;

class ItemRevolverBullet extends ItemBullet
{
    public function __construct() {
        parent::__construct(BulletId::REVOLVER, "RevolverBullet", new RevolverBullet());
    }
}