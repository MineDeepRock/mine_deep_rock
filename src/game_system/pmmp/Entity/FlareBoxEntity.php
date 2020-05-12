<?php


namespace game_system\pmmp\Entity;


use game_system\interpreter\FlareBoxInterpreter;
use game_system\model\Coordinate;
use game_system\service\GameScoresService;
use game_system\service\UsersService;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

class FlareBoxEntity extends BoxEntity
{
    public $skinName = "FlareBox";
    public $geometryId = "geometry.FlareBox";
    public $geometryName = "FlareBox.geo.json";

    private $interpreter;
    private $handler;

    public function __construct(
        Level $level,
        Player $owner,
        UsersService $usersService,
        GameScoresService $gameScoresService,
        TaskScheduler $scheduler) {

        parent::__construct($level, $owner, $scheduler);
        $this->interpreter = new FlareBoxInterpreter(
            $owner,
            $usersService,
            $gameScoresService,
            new Coordinate(
                $this->getX(),
                $this->getY(),
                $this->getZ()),
            $scheduler);

        $this->handler = $scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick): void {
            if ($this->isAlive()) $this->kill();
        }), 20 * $this->interpreter->getFlareBox()->getSecondLimit());
    }

    protected function onDeath(): void {
        $this->handler->cancel();
        $this->interpreter->stop();
        parent::onDeath();
    }

    public function getName(): string {
        return "FlareBox";
    }
}