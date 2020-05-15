<?php


namespace game_system\pmmp\client;


use pocketmine\level\Level;
use pocketmine\level\particle\HugeExplodeParticle;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

class FragGrenadeClient
{
    public function explodeParticle(Level $level, Vector3 $pos): void {
        $level->addParticle(new HugeExplodeParticle($pos));
    }

    public function playSound(Level $level, Vector3 $pos): void {
        $players = $level->getPlayers();

        foreach ($players as $player) {
            $packet = new PlaySoundPacket();
            $packet->x = $pos->x;
            $packet->y = $pos->y;
            $packet->z = $pos->z;
            $packet->volume = 3;
            $packet->pitch = 2;
            $packet->soundName = "random.explode";
            $player->sendDataPacket($packet);
        }
    }
}