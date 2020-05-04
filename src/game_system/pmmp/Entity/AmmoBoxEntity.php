<?php


namespace game_system\pmmp\Entity;


use game_system\pmmp\client\AmmoBoxClient;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

class AmmoBoxEntity extends BoxEntity
{
    public $skinName = "AmmoBox";
    public $geometryId = "geometry.AmmoBox";
    public $geometryName = "AmmoBox.geo.json";

    private $ammoBoxClient;

    public function __construct(Level $level, CompoundTag $nbt, TaskScheduler $scheduler) {
        parent::__construct($level, $nbt);
        $this->ammoBoxClient = new AmmoBoxClient();

        $scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick): void {
            if ($this->isAlive()) {
                $this->kill();
            }
        }), 20 * $this->ammoBoxClient->getAmmoBox()->getSecondLimit());
    }

    public function getName(): string {
        return "AmmoBox";
    }

    public function onTap(Player $player): void {
        $this->ammoBoxClient->useAmmoBox($player);
    }
}