<?php


namespace gun_system\pmmp\items\bullet;


use gun_system\models\attachment\bullet\HandGunBullet;
use gun_system\models\BulletId;

class ItemHandGunBullet extends ItemBullet
{
    public function __construct() {
        parent::__construct(BulletId::HAND_GUN, "HandGunBullet",new HandGunBullet());
    }
}