<?php


namespace gun_system\pmmp\items\sniper_rifle;


use gun_system\models\GunId;
use gun_system\models\sniper_rifle\Gehenna;
use gun_system\pmmp\items\ItemSniperRifle;
use pocketmine\scheduler\TaskScheduler;

class ItemGehenna extends ItemSniperRifle
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(GunId::GEHENNA, "Gehenna", new Gehenna($scheduler));
    }
}