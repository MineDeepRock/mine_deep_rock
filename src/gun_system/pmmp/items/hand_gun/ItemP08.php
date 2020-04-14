<?php


namespace gun_system\pmmp\items\hand_gun;


use gun_system\models\GunId;
use gun_system\models\hand_gun\P08;
use gun_system\pmmp\items\ItemHandGun;
use pocketmine\scheduler\TaskScheduler;

class ItemP08 extends ItemHandGun
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(GunId::P08, "P08", new P08($scheduler));
    }
}