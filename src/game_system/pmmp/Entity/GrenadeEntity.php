<?php


namespace game_system\pmmp\Entity;


use game_system\interpreter\GrenadeBaseInterpreter;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Throwable;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\level\Level;
use pocketmine\math\RayTraceResult;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;

class GrenadeEntity extends Throwable
{
    public const NETWORK_ID = self::SNOWBALL;

    protected $gravity = 0.06;

    /**
     * @var GrenadeBaseInterpreter
     */
    protected $interpreter;

    public function __construct(Level $level, Entity $shootingEntity = null) {
        $nbt = new CompoundTag('', [
            'Pos' => new ListTag('Pos', [
                new DoubleTag('', $shootingEntity->getX()),
                new DoubleTag('', $shootingEntity->getY() + $shootingEntity->getEyeHeight()),
                new DoubleTag('', $shootingEntity->getZ())
            ]),
            'Motion' => new ListTag('Motion', [
                new DoubleTag('', $shootingEntity->getDirectionVector()->getX()),
                new DoubleTag('', $shootingEntity->getDirectionVector()->getY()),
                new DoubleTag('', $shootingEntity->getDirectionVector()->getZ())
            ]),
            'Rotation' => new ListTag('Rotation', [
                new FloatTag("", $shootingEntity->getYaw()),
                new FloatTag("", $shootingEntity->getPitch())
            ]),
        ]);
        parent::__construct($level, $nbt, $shootingEntity);
    }

    protected function onHitBlock(Block $blockHit, RayTraceResult $hitResult): void {
        $this->interpreter->explode($this->getPosition(), function () { $this->kill(); });
        return;
    }

    protected function onHit(ProjectileHitEvent $event): void {
        return;
    }

    protected function onHitEntity(Entity $entityHit, RayTraceResult $hitResult): void {
        $this->interpreter->explode($this->getPosition(), function () { $this->kill(); });
        return;
    }
}