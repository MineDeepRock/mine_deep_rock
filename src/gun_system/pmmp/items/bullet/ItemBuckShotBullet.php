<?php


namespace gun_system\pmmp\items\bullet;


use gun_system\models\attachment\bullet\BuckShotBullet;
use gun_system\models\BulletId;

class ItemBuckShotBullet extends ItemBullet
{
    public function __construct() { parent::__construct(BulletId::BUCK_SHOT, "BuckShotBullet", new BuckShotBullet()); }
}