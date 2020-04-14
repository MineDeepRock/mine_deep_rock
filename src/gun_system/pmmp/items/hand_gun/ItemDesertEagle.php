<?php


namespace gun_system\pmmp\items\hand_gun;


use gun_system\models\GunId;
use gun_system\models\hand_gun\DesertEagle;
use gun_system\pmmp\items\ItemHandGun;
use pocketmine\scheduler\TaskScheduler;

class ItemDesertEagle extends ItemHandGun
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(GunId::M1911,"DesertEagle", new DesertEagle($scheduler));
    }
}