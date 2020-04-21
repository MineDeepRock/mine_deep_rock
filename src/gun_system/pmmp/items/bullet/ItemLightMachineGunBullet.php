<?php


namespace gun_system\pmmp\items\bullet;


use gun_system\models\attachment\bullet\LightMachineGunBullet;
use gun_system\models\BulletId;

class ItemLightMachineGunBullet extends ItemBullet
{
    public function __construct() { parent::__construct(BulletId::LMG, "LightMachineGunBullet", new LightMachineGunBullet()); }
}