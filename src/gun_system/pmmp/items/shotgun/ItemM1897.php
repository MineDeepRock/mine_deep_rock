<?php


namespace gun_system\pmmp\items\shotgun;


use gun_system\models\GunId;
use gun_system\models\shotgun\M1897;
use gun_system\pmmp\items\Bullet;
use gun_system\pmmp\items\ItemShotgun;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class ItemM1897 extends ItemShotgun
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(GunId::M1897, "M1897", new M1897($scheduler));
    }
}