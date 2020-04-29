<?php


namespace gun_system\interpreter;


use gun_system\models\sub_machine_gun\SubMachineGun;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class SubMachineGunInterpreter extends GunInterpreter
{
    public function __construct(SubMachineGun $gun, Player $owner, TaskScheduler $scheduler) {
        parent::__construct($gun, $owner, $scheduler);
    }
}