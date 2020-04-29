<?php


namespace gun_system\interpreter;


use gun_system\models\sniper_rifle\SniperRifle;
use gun_system\pmmp\GunSounds;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class SniperRifleInterpreter extends GunInterpreter
{
    public function __construct(SniperRifle $gun, Player $owner, TaskScheduler $scheduler) {
        parent::__construct($gun, $owner, $scheduler);
        $this->setWhenBecomeReady(function(){
            GunSounds::play($this->owner,GunSounds::SniperRifleCocking());
        });
    }
}