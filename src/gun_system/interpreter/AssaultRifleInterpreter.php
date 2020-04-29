<?php


namespace gun_system\interpreter;


use gun_system\models\assault_rifle\AssaultRifle;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class AssaultRifleInterpreter extends GunInterpreter
{
    public function __construct(AssaultRifle $gun, Player $owner, TaskScheduler $scheduler) {
        parent::__construct($gun, $owner, $scheduler);
    }
}