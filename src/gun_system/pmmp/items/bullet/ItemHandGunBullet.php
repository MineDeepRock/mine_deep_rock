<?php


namespace gun_system\pmmp\items\bullet;


use gun_system\models\bullet\HandGunBullet;
use gun_system\models\BulletId;
use gun_system\pmmp\items\ItemBullet;

class ItemHandGunBullet extends ItemBullet
{
    public function __construct() {
        parent::__construct(BulletId::HAND_GUN, "HandGunBullet",new HandGunBullet());
    }
}