<?php


namespace game_system\pmmp\client;


use game_system\model\SmokeGrenade;
use pocketmine\level\Level;
use pocketmine\level\particle\MobSpawnParticle;
use pocketmine\math\Vector3;

class SmokeGrenadeClient
{
    public function explodeParticle(Level $level, Vector3 $pos): void {
        $level->addParticle(new MobSpawnParticle($pos,SmokeGrenade::RANGE,3));
    }

    public function playSound(Level $level, Vector3 $pos): void {
        //TODO:実装
    }
}