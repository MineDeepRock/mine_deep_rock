<?php


namespace gun_system\pmmp\entity;


use gun_system\models\GunPrecision;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Throwable;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\item\Egg;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\level\Level;
use pocketmine\level\particle\CriticalParticle;
use pocketmine\level\particle\ItemBreakParticle;
use pocketmine\math\Vector3;
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
    static function spawn(Player $player, float $speed, GunPrecision $precision, TaskScheduler $scheduler, $isArrow = false) {
        $aimPos = $player->getDirectionVector();
        $value = $player->isSneaking() ? $precision->getADS() : $precision->getHipShooting();

        $nbt = new CompoundTag("", [
            "Pos" => new ListTag("Pos", [
                new DoubleTag("", $player->x),
                new DoubleTag("", $player->y + $player->getEyeHeight() - 0.3),
                new DoubleTag("", $player->z)
            ]),
            "Motion" => new ListTag("Motion", [
                new DoubleTag("", $aimPos->x + rand(-(100 - $value), (100 - $value)) / 200),
                new DoubleTag("", $aimPos->y + rand(-(100 - $value), (100 - $value)) / 200),
                new DoubleTag("", $aimPos->z + rand(-(100 - $value), (100 - $value)) / 200)
            ]),
            "Rotation" => new ListTag("Rotation", [
                new FloatTag("", $player->yaw),
                new FloatTag("", $player->pitch)
            ]),
        ]);

        $projectile = $isArrow ? Entity::createEntity("Arrow", $player->getLevel(), $nbt, $player) : Entity::createEntity("Egg", $player->getLevel(), $nbt, $player);
        $projectile->setMotion($projectile->getMotion()->multiply($speed / 27.8));

        $handle = $scheduler->scheduleRepeatingTask(new ClosureTask(
            function (int $currentTick) use ($projectile) : void {
                if (!$projectile->isClosed()){
                    $projectile->getLevel()->addParticle(new CriticalParticle(new Vector3(
                        $projectile->getX(),
                        $projectile->getY(),
                        $projectile->getZ()
                    ),1));
                }
            }
        ), 1);

        //卵の速さが毎秒２７ブロック
        $projectile->spawnToAll();
        $scheduler->scheduleDelayedTask(new ClosureTask(
            function (int $currentTick) use ($projectile,$handle) : void {
                $handle->cancel();
                if (!$projectile->isClosed())
                    $projectile->close();
            }
        ), 20 * 5);
    }
}

