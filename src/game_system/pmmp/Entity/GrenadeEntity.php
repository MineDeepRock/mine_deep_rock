<?php


namespace game_system\pmmp\Entity;


use game_system\interpreter\GrenadeBaseInterpreter;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class GrenadeEntity extends GadgetEntity
{
    /**
     * @var GrenadeBaseInterpreter
     */
    protected $interpreter;
    private $isAvailable = true;

    public function __construct(Level $level, Player $owner, TaskScheduler $scheduler) {
        $nbt = new CompoundTag('', [
            'Pos' => new ListTag('Pos', [
                new DoubleTag('', $owner->getX()),
                new DoubleTag('', $owner->getY() + $owner->getEyeHeight()),
                new DoubleTag('', $owner->getZ())
            ]),
            'Motion' => new ListTag('Motion', [
                new DoubleTag('', $owner->getDirectionVector()->getX()),
                new DoubleTag('', $owner->getDirectionVector()->getY()),
                new DoubleTag('', $owner->getDirectionVector()->getZ())
            ]),
            'Rotation' => new ListTag('Rotation', [
                new FloatTag("", $owner->getYaw()),
                new FloatTag("", $owner->getPitch())
            ]),
        ]);
        parent::__construct($level, $owner->getName(), $scheduler, $nbt);
        $this->setMotion($this->getMotion()->multiply(1.5));
    }

    public function explode(): void {
        $this->interpreter->explode($this->getPosition(), function () { $this->kill(); });
    }

    public function entityBaseTick(int $tickDiff = 1): bool {
        if ($this->isOnGround() && $this->isAvailable) {
            $this->isAvailable = false;
            $this->explode();
        }
        return parent::entityBaseTick($tickDiff);
    }
}