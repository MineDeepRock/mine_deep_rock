<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\pmmp\entity\TeamDeathMatchNPC;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;

class SpawnTeamDeathMatchNPCPMMPService
{
    static function execute(Level $level, Vector3 $vector3, int $yaw): void {
        $nbt = new CompoundTag('', [
            'Pos' => new ListTag('Pos', [
                new DoubleTag('', $vector3->getX()),
                new DoubleTag('', $vector3->getY() + 0.5),
                new DoubleTag('', $vector3->getZ())
            ]),
            'Motion' => new ListTag('Motion', [
                new DoubleTag('', 0),
                new DoubleTag('', 0),
                new DoubleTag('', 0)
            ]),
            'Rotation' => new ListTag('Rotation', [
                new FloatTag("", $yaw),
                new FloatTag("", 0)
            ]),
        ]);
        $npc = new TeamDeathMatchNPC($level, $nbt);
        $npc->setNameTag("チームデスマッチに参加");
        $npc->spawnToAll();
    }
}