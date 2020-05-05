<?php


namespace game_system\pmmp\Entity;


use game_system\interpreter\AmmoBoxInterpreter;
use game_system\model\Coordinate;
use game_system\pmmp\items\SpawnAmmoBoxItem;
use game_system\service\UsersService;
use game_system\service\WeaponsService;
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

    private $owner;
    private $scheduler;
    private $interpreter;

    public function __construct(
        Level $level,
        CompoundTag $nbt,
        Player $owner,
        UsersService $usersService,
        WeaponsService $weaponService,
        TaskScheduler $scheduler) {
        parent::__construct($level, $nbt);
        $this->owner = $owner;
        $this->scheduler = $scheduler;
        $this->interpreter = new AmmoBoxInterpreter(
            $owner,
            new Coordinate(
                $this->getX(),
                $this->getY(),
                $this->getZ()),
            $usersService,
            $weaponService,
            $scheduler);

        $scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick): void {
            if ($this->isAlive()) {
                $this->kill();
                $this->giveAgain();
            }
        }), 20 * $this->interpreter->getAmmoBox()->getSecondLimit());
    }

    protected function onDeath(): void {
        parent::onDeath();
        $this->interpreter->stop();
        $this->giveAgain();
    }

    public function giveAgain(): void {
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick): void {
            if (!$this->owner->isOnline()) return;
            if ($this->owner->getGamemode() !== Player::ADVENTURE) return;

            $contain = $this->owner->getInventory()->contains(new SpawnAmmoBoxItem());
            if ($contain) return;

            $this->owner->getInventory()->addItem(new SpawnAmmoBoxItem());
        }), 20 * 10);
    }

    public function getName(): string {
        return "AmmoBox";
    }
}