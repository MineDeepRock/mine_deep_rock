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
    static function spawn(Player $player, float $speed, float $precision) {
        $aimPos = $player->getDirectionVector();

        $nbt = new CompoundTag("", [
            "Pos" => new ListTag("Pos", [
                new DoubleTag("", $player->x),
                new DoubleTag("", $player->y + $player->getEyeHeight()),
                new DoubleTag("", $player->z)
            ]),
            "Motion" => new ListTag("Motion", [
                new DoubleTag("", $aimPos->x + rand(-(100 - $precision), (100 - $precision)) / 200),
                new DoubleTag("", $aimPos->y + rand(-(100 - $precision), (100 - $precision)) / 200),
                new DoubleTag("", $aimPos->z + rand(-(100 - $precision), (100 - $precision)) / 200)
            ]),
            "Rotation" => new ListTag("Rotation", [
                new FloatTag("", $player->yaw),
                new FloatTag("", $player->pitch)
            ]),
        ]);
        $projectile = Entity::createEntity("Egg", $player->getLevel(), $nbt, $player);
        $projectile->setMotion($projectile->getMotion()->multiply($speed));
        $projectile->spawnToAll();
    }
}