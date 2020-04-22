<?php


namespace gun_system\pmmp\items\bullet;


use gun_system\models\attachment\bullet\SlugBullet;
use gun_system\models\BulletId;

class ItemSlugBullet extends ItemBullet
{
    public function __construct() { parent::__construct(BulletId::SLUG, "SlugBullet", new SlugBullet()); }
}