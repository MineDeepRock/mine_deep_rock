<?php


namespace game_system\pmmp\Entity;


use game_system\interpreter\MedicineBoxInterpreter;
use game_system\model\Coordinate;
use game_system\model\MedicineBox;
use game_system\service\GameScoresService;
use game_system\service\UsersService;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

class MedicineBoxEntity extends BoxEntity
{
    public $skinName = "MedicineBox";
    public $geometryId = "geometry.MedicineBox";
    public $geometryName = "MedicineBox.geo.json";

    private $interpreter;
    private $handler;

    public function __construct(
        Level $level,
        Player $owner,
        UsersService $usersService,
        GameScoresService $gameScoresService,
        TaskScheduler $scheduler) {
        parent::__construct($level, $owner, $scheduler);
        $this->interpreter = new MedicineBoxInterpreter(
            $owner,
            new Coordinate(
                $this->getX(),
                $this->getY(),
                $this->getZ()),
            $usersService,
            $gameScoresService,
            $scheduler);

        $this->handler = $scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick): void {
            if ($this->isAlive()) $this->kill();
        }), 20 * MedicineBox::SECOND_LIMIT);
    }

    protected function onDeath(): void {
        $this->interpreter->stop();
        $this->handler->cancel();
        parent::onDeath();
    }

    public function getName(): string {
        return "MedicineBox";
    }
}