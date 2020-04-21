<?php


namespace gun_system\pmmp\items\bullet;


use gun_system\models\attachment\bullet\SniperRifleBullet;
use gun_system\models\BulletId;

class ItemSniperRifleBullet extends ItemBullet
{
    public function __construct() {
        parent::__construct(BulletId::SNIPER_RIFLE, "SniperRifleBullet",new SniperRifleBullet());
    }
}