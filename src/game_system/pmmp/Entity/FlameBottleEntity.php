<?php


namespace game_system\pmmp\Entity;


use game_system\interpreter\FlameBottleInterpreter;
use game_system\service\GameScoresService;
use game_system\service\UsersService;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class FlameBottleEntity extends GrenadeEntity
{
    public $skinName = "FlameBottle";
    public $geometryId = "geometry.FlameBottle";
    public $geometryName = "FlameBottle.geo.json";

    public function __construct(Level $level,
                                Player $owner,
                                UsersService $usersService,
                                GameScoresService $gameScoresService,
                                TaskScheduler $scheduler) {
        parent::__construct($level, $owner);
        $this->interpreter = new FlameBottleInterpreter(
            $owner,
            $usersService,
            $gameScoresService,
            $scheduler);
    }

    protected function onDeath(): void {
        $this->interpreter->giveAgain();
        parent::onDeath();
    }
}