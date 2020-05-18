<?php


namespace game_system\pmmp\Entity;


use game_system\interpreter\SmokeGrenadeInterpreter;
use game_system\model\SmokeGrenade;
use game_system\service\GameScoresService;
use game_system\service\UsersService;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

class SmokeGrenadeEntity extends GrenadeEntity
{
    public $skinName = "SmokeGrenade";
    public $geometryId = "geometry.SmokeGrenade";
    public $geometryName = "SmokeGrenade.geo.json";

    public function __construct(Level $level,
                                Player $owner,
                                UsersService $usersService,
                                GameScoresService $gameScoresService,
                                TaskScheduler $scheduler) {
        parent::__construct($level, $owner, $scheduler);
        $this->interpreter = new SmokeGrenadeInterpreter(
            $owner,
            $usersService,
            $gameScoresService,
            $scheduler);


        $scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick): void {
            if ($this->isAlive()) $this->kill();
        }), 20 * SmokeGrenade::DURATION);
    }

    protected function onDeath(): void {
        $this->interpreter->stop();
        $this->interpreter->giveAgain();
        parent::onDeath();
    }
}