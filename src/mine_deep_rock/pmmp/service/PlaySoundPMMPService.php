<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;

class PlaySoundPMMPService
{
    static function execute(Player $player, Vector3 $pos, string $name, int $volume = 50, int $pitch = 1): void {
        $packet = new PlaySoundPacket();
        $packet->x = $pos->x;
        $packet->y = $pos->y;
        $packet->z = $pos->z;
        $packet->volume = 50;
        $packet->pitch = 1;
        $packet->soundName = $name;
        $player->sendDataPacket($packet);
    }
}