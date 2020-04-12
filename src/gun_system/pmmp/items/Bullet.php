<?php


namespace gun_system\pmmp\items;


use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;

class Bullet
{
    static function spawn(Player $player){
        $aimPos = $player->getDirectionVector();

        $nbt = new CompoundTag("", [
            "Pos" => new ListTag("Pos", [
                new DoubleTag("", $player->x+0.5),
                new DoubleTag("", $player->y + $player->getEyeHeight()),
                new DoubleTag("", $player->z)
            ]),
            "Motion" => new ListTag("Motion", [
                new DoubleTag("", $aimPos->x),
                new DoubleTag("", $aimPos->y),
                new DoubleTag("", $aimPos->z)
            ]),
            "Rotation" => new ListTag("Rotation", [
                new FloatTag("", $player->yaw),
                new FloatTag("", $player->pitch)
            ]),
        ]);
        $projectile = Entity::createEntity("Egg", $player->getLevel(), $nbt, $player);
        $f = 1.5;
        $projectile->setMotion($projectile->getMotion()->multiply($f));
        $projectile->spawnToAll();
    }
}