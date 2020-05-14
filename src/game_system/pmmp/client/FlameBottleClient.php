<?php


namespace game_system\pmmp\client;


use pocketmine\level\Level;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\particle\HugeExplodeParticle;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

class FlameBottleClient
{
    public function explodeParticle(Level $level, Vector3 $pos): void {
        $level->addParticle(new FlameParticle($pos));
    }

    public function playSound(Level $level, Vector3 $pos): void {
        //TODO:実装
    }
}