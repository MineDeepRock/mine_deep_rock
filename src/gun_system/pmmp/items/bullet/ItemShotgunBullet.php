<?php


namespace gun_system\pmmp\items\bullet;


use gun_system\models\bullet\ShotgunBullet;
use gun_system\models\BulletId;
use gun_system\pmmp\items\ItemBullet;

class ItemShotgunBullet extends ItemBullet
{
    public function __construct() {
        parent::__construct(BulletId::SHOTGUN, "ShotgunBullet",new ShotgunBullet());
    }
}