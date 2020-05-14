<?php


namespace game_system\pmmp\Entity;


use game_system\interpreter\FlareBoxInterpreter;
use game_system\model\Coordinate;
use game_system\model\FlareBox;
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
    private $isOnGroundHandler;

    public function __construct(
        Level $level,
        Player $owner,
        UsersService $usersService,
        GameScoresService $gameScoresService,
        TaskScheduler $scheduler) {
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

        parent::__construct($level, $owner, $scheduler, $nbt);
        $this->setMotion($this->getMotion()->multiply(1.5));


        $this->isOnGroundHandler = $scheduler->scheduleRepeatingTask(new ClosureTask(function (int $tick) use ($owner, $usersService, $gameScoresService, $scheduler): void {
            if ($this->isOnGround()) {
                $this->interpreter = new FlareBoxInterpreter(
                    new Coordinate(
                        $this->getX(),
                        $this->getY(),
                        $this->getZ()),
                    $owner,
                    $usersService,
                    $gameScoresService,
                    $scheduler);
                $this->isOnGroundHandler->cancel();
            }
        }), 20 * 0.5);

        $this->handler = $scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick): void {
            if ($this->isAlive()) $this->kill();
        }), 20 * FlareBox::SECOND_LIMIT);
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