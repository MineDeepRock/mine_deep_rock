<?php


namespace gun_system\pmmp\items\bullet;


use gun_system\models\attachment\bullet\AssaultRifleBullet;
use gun_system\models\BulletId;

class ItemAssaultRifleBullet extends ItemBullet
{
    public function __construct() {
        parent::__construct(BulletId::ASSAULT_RIFLE, "AssaultRifleBullet",new AssaultRifleBullet());
    }
}