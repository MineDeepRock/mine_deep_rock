<?php


namespace game_system\pmmp\Entity;


use game_system\interpreter\AmmoBoxInterpreter;
use game_system\model\AmmoBox;
use game_system\model\Coordinate;
use game_system\service\GameScoresService;
use game_system\service\UsersService;
use game_system\service\WeaponsService;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

class AmmoBoxEntity extends BoxEntity
{
    public $skinName = "AmmoBox";
    public $geometryId = "geometry.AmmoBox";
    public $geometryName = "AmmoBox.geo.json";

    private $interpreter;
    private $handler;

    public function __construct(
        Level $level,
        Player $owner,
        UsersService $usersService,
        WeaponsService $weaponService,
        GameScoresService $gameScoresService,
        TaskScheduler $scheduler) {
        parent::__construct($level, $owner, $scheduler);
        $this->interpreter = new AmmoBoxInterpreter(
            $owner,
            $usersService,
            $weaponService,
            $gameScoresService,
            $scheduler);

        $this->interpreter->carryOut($this);

        $this->handler = $scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick): void {
            if ($this->isAlive()) $this->kill();
        }), 20 * AmmoBox::SECOND_LIMIT);
    }

    protected function onDeath(): void {
        $this->handler->cancel();
        $this->interpreter->stop();
        parent::onDeath();
    }

    public function getName(): string {
        return "AmmoBox";
    }
}