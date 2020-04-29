<?php


namespace gun_system\interpreter;


use gun_system\models\hand_gun\HandGun;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class HandGunInterpreter extends GunInterpreter
{
    public function __construct(HandGun $gun, Player $owner, TaskScheduler $scheduler) {
        parent::__construct($gun, $owner, $scheduler);
    }
}