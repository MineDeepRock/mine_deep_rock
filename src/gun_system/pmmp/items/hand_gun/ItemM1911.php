<?php


namespace gun_system\pmmp\items\hand_gun;


use gun_system\models\GunId;
use gun_system\models\hand_gun\M1911;
use gun_system\pmmp\items\ItemHandGun;
use pocketmine\scheduler\TaskScheduler;

class ItemM1911 extends ItemHandGun
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(GunId::DESERT_EAGLE, "M1911", new M1911($scheduler));
    }
}