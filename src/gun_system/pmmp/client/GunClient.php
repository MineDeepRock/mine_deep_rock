<?php


namespace gun_system\pmmp\client;


use Closure;
use gun_system\models\BulletId;
use gun_system\models\Gun;
use gun_system\models\shotgun\Shotgun;
use gun_system\pmmp\GunSounds;
use pocketmine\entity\Entity;
use pocketmine\level\particle\CriticalParticle;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

class GunClient
{
    private $owner;
    private $gun;
    private $handler;

    public function __construct(Player $owner, Gun $gun) {
        $this->owner = $owner;
        $this->gun = $gun;
    }

    private function playShootingSound(): void {
        $soundName = GunSounds::shootSoundFromGunType($this->gun->getType());
        GunSounds::playAround($this->owner, $soundName);
    }

    public function scare(TaskScheduler $scheduler, Closure $onFinished) {
        if ($this->handler !== null) {
            $this->handler->cancel();
        }

        $this->handler = $scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($onFinished) : void {
            if ($this->owner->isOnline()) {
                $onFinished();
            }
        }), 20 * 5);
    }

    public function shoot(int $currentBullet, int $magazineCapacity, TaskScheduler $scheduler): void {

        //スペクテイターのときは打てないように
        if ($this->owner->getGamemode() === Player::SPECTATOR) {
            return;
        }
        if ($this->gun instanceof Shotgun) {
            if ($this->gun->getBulletType()->equal(BulletId::SHOTGUN)) {
                $i = 0;
                while ($i < $this->gun->getPellets()) {
                    self::spawnBullet($scheduler);
                    $i++;
                }
                $this->doReaction();
            }
        } else {
            self::spawnBullet($scheduler);
        }
        $this->owner->sendPopup($currentBullet . "/" . $magazineCapacity);
        $this->playShootingSound();
        $this->doReaction();
    }

    private function doReaction(): void {
        $player = $this->owner;
        $reaction = $this->gun->getReaction();
        if ($reaction !== 0.0 && !$this->owner->isSneaking()) {
            $playerPosition = $player->getLocation();
            $dir = -$playerPosition->getYaw() - 90.0;
            $pitch = -$playerPosition->getPitch() - 180.0;
            $xd = $reaction * $reaction * cos(deg2rad($dir)) * cos(deg2rad($pitch)) / 6;
            $zd = $reaction * $reaction * -sin(deg2rad($dir)) * cos(deg2rad($pitch)) / 6;

            $vec = new Vector3($xd, 0, $zd);
            $vec->multiply(3);
            $player->setMotion($vec);
        }
    }

    private function spawnBullet(TaskScheduler $scheduler) {
        $player = $this->owner;
        $precision = $this->gun->getPrecision();

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

        $projectile = Entity::createEntity("Egg", $player->getLevel(), $nbt, $player);
        $projectile->setMotion($projectile->getMotion()->multiply($this->gun->getBulletSpeed()->getPerSecond() / 27.8 / 3));

        $handle = $scheduler->scheduleRepeatingTask(new ClosureTask(
            function (int $currentTick) use ($projectile) : void {
                if (!$projectile->isClosed()) {
                    $projectile->getLevel()->addParticle(new CriticalParticle(new Vector3(
                        $projectile->getX(),
                        $projectile->getY(),
                        $projectile->getZ()
                    ), 4));
                }
            }
        ), 1);

        //卵の速さが毎秒２７ブロック
        $projectile->spawnToAll();
        $scheduler->scheduleDelayedTask(new ClosureTask(
            function (int $currentTick) use ($projectile, $handle) : void {
                $handle->cancel();
                if (!$projectile->isClosed())
                    $projectile->close();
            }
        ), 20 * 5);
    }
}