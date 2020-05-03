<?php


namespace gun_system\pmmp\items\bullet;


use gun_system\models\attachment\bullet\ShotgunBullet;
use gun_system\models\attachment\bullet\SniperRifleBullet;
use gun_system\models\BulletId;

class ItemShotgunBullet extends ItemBullet
{
    public function __construct() {
        parent::__construct(BulletId::SHOTGUN, "ShotgunBullet",new ShotgunBullet());
    }
}