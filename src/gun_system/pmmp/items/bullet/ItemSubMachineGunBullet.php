<?php


namespace gun_system\pmmp\items\bullet;


use gun_system\models\attachment\bullet\SubMachineGunBullet;
use gun_system\models\BulletId;

class ItemSubMachineGunBullet extends ItemBullet
{
    public function __construct() {
        parent::__construct(BulletId::SMG, "SubMachineGunBullet",new SubMachineGunBullet());
    }
}