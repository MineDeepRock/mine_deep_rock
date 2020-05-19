<?php


namespace game_system\pmmp\client;


use pocketmine\level\Level;
use pocketmine\level\particle\LavaParticle;
use pocketmine\math\Vector3;

class FlameBottleClient
{
    public function explodeParticle(Level $level, Vector3 $pos): void {
        $level->addParticle(new LavaParticle($pos));
    }

    public function playSound(Level $level, Vector3 $pos): void {
        //TODO:実装
    }
}