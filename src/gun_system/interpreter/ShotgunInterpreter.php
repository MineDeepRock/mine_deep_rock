<?php


namespace gun_system\interpreter;


use gun_system\models\shotgun\Shotgun;
use gun_system\pmmp\GunSounds;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class ShotgunInterpreter extends GunInterpreter
{
    public function __construct(Shotgun $gun, Player $owner, TaskScheduler $scheduler) {
        parent::__construct($gun, $owner, $scheduler);
        $this->setWhenBecomeReady(function(){
            GunSounds::play($this->owner,GunSounds::ShotgunPumpAction());
        });
    }
}