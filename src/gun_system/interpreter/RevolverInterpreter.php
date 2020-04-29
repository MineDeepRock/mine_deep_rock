<?php


namespace gun_system\interpreter;


use gun_system\models\revolver\Revolver;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class RevolverInterpreter extends GunInterpreter
{
    public function __construct(Revolver $gun, Player $owner, TaskScheduler $scheduler) {
        parent::__construct($gun, $owner, $scheduler);
    }
}