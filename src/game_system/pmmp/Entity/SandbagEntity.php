<?php


namespace game_system\pmmp\Entity;


use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

class SandbagEntity extends GadgetEntity
{
    public $skinName = "Sandbag";
    public $geometryId = "geometry.Sandbag";
    public $geometryName = "Sandbag.geo.json";
    public $width = 2;
    public $height = 1.5;
    public $scale = 1;
    protected $gravity = 2;

    public $defaultHP = 20;

    private $handler;

    public function __construct(Level $level, Player $player, TaskScheduler $scheduler) {
        $nbt = new CompoundTag('', [
            'Pos' => new ListTag('Pos', [
                new DoubleTag('', $player->getX()),
                new DoubleTag('', $player->getY()),
                new DoubleTag('', $player->getZ())
            ]),
            'Motion' => new ListTag('Motion', [
                new DoubleTag('', 0),
                new DoubleTag('', 0),
                new DoubleTag('', 0)
            ]),
            'Rotation' => new ListTag('Rotation', [
                new FloatTag("", $player->getYaw()),
                new FloatTag("", 0)
            ]),
        ]);
        parent::__construct($level, $player->getName(), $scheduler, $nbt);

        $this->handler = $scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick): void {
            if ($this->isAlive()) $this->kill();
        }), 20 * 100);
    }

    protected function onDeath(): void {
        $this->handler->cancel();
        parent::onDeath();
    }
}