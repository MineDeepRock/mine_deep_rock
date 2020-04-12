<?php


namespace gun_system\pmmp\items;


use gun_system\models\HandGun;
use pocketmine\scheduler\TaskScheduler;

class ItemHandGun extends ItemGun
{
    public function __construct(int $id, TaskScheduler $scheduler) {
        parent::__construct($id,"HandGun", new HandGun($scheduler));
    }
}