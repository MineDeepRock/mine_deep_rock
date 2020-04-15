<?php


namespace gun_system\pmmp\items\bullet;


use gun_system\models\bullet\AssaultRifleBullet;
use gun_system\models\BulletId;
use gun_system\pmmp\items\ItemBullet;

class ItemAssaultRifleBullet extends ItemBullet
{
    public function __construct() {
        parent::__construct(BulletId::ASSAULT_RIFLE, "AssaultRifleBullet",new AssaultRifleBullet());
    }
}