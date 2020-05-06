<?php


namespace game_system\pmmp\Entity;


use game_system\interpreter\FlareBoxInterpreter;
use game_system\model\Coordinate;
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

    private $owner;
    private $scheduler;
    private $interpreter;

    public function __construct(
        Level $level,
        Player $owner,
        UsersService $usersService,
        TaskScheduler $scheduler) {
        $nbt = new CompoundTag('', [
            'Pos' => new ListTag('Pos', [
                new DoubleTag('', $owner->getX()),
                new DoubleTag('', $owner->getY() + 0.5),
                new DoubleTag('', $owner->getZ())
            ]),
            'Motion' => new ListTag('Motion', [
                new DoubleTag('', 0),
                new DoubleTag('', 0),
                new DoubleTag('', 0)
            ]),
            'Rotation' => new ListTag('Rotation', [
                new FloatTag("", $owner->getYaw()),
                new FloatTag("", $owner->getPitch())
            ]),
        ]);

        parent::__construct($level, $nbt);
        $this->owner = $owner;
        $this->scheduler = $scheduler;

        $this->interpreter = new FlareBoxInterpreter(
            $owner,
            $usersService,
            new Coordinate(
                $this->getX(),
                $this->getY(),
                $this->getZ()),
            $scheduler);

        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick): void {
            $this->kill();
        }), 20 * 20);
    }

    protected function onDeath(): void {
        $this->interpreter->stop();
        parent::onDeath();
    }

    public function getName(): string {
        return "FlareBox";
    }
}