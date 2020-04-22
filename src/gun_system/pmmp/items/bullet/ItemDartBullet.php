<?php


namespace gun_system\pmmp\items\bullet;


use gun_system\models\attachment\bullet\DartBullet;
use gun_system\models\BulletId;


class ItemDartBullet extends ItemBullet
{
    public function __construct() { parent::__construct(BulletId::DART, "DartBullet", new DartBullet()); }
}