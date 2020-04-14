<?php


namespace gun_system\pmmp\items\hand_gun;


use gun_system\models\GunId;
use gun_system\models\hand_gun\DesertEagle;
use gun_system\pmmp\items\ItemGun;
use pocketmine\scheduler\TaskScheduler;

class ItemDesertEagle extends ItemGun
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(GunId::M1911,"DesertEagle", new DesertEagle($scheduler));
    }
}