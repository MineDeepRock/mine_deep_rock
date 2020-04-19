<?php


namespace gun_system\pmmp\items\bullet;


use gun_system\models\bullet\SubMachineGunBullet;
use gun_system\models\BulletId;
use gun_system\pmmp\items\ItemBullet;

class ItemSubMachineGunBullet extends ItemBullet
{
    public function __construct() {
        parent::__construct(BulletId::SMG, "SubMachineGunBullet",new SubMachineGunBullet());
    }
}