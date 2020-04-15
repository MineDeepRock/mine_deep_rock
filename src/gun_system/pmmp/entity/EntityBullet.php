<?php


namespace gun_system\pmmp\entity;


use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

//TODO:場所ちがくね？
class EntityBullet
{
    static function spawn(Player $player, float $speed, float $precision,int $range,TaskScheduler $scheduler) {
        $aimPos = $player->getDirectionVector();

        $nbt = new CompoundTag( "", [
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
        $projectile->setMotion($projectile->getMotion()->multiply($speed/27));
        //卵の速さが毎秒２７ブロック
        $projectile->spawnToAll();
        $scheduler->scheduleDelayedTask(new ClosureTask(
            function (int $currentTick) use ($projectile) : void {
                if (!$projectile->isClosed())
                    $projectile->close();
            }
        ), 20 * ($range/$speed)*2);
    }
}