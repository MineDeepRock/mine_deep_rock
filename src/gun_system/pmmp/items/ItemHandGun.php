<?php


namespace gun_system\pmmp\items;


use gun_system\models\HandGun;
use pocketmine\scheduler\TaskScheduler;

class ItemHandGun extends ItemGun
{
    public function __construct(TaskScheduler $scheduler) {
        parent::__construct(HandGun::getId(),"HandGun", new HandGun($scheduler));
    }
}