<?php


namespace game_system\pmmp\Entity;


use game_system\interpreter\FragGrenadeInterpreter;
use game_system\service\GameScoresService;
use game_system\service\UsersService;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class FragGrenadeEntity extends GrenadeEntity
{
    public $skinName = "FragGrenade";
    public $geometryId = "geometry.FragGrenade";
    public $geometryName = "FragGrenade.geo.json";

    public function __construct(Level $level,
                                Player $owner,
                                UsersService $usersService,
                                GameScoresService $gameScoresService,
                                TaskScheduler $scheduler) {
        parent::__construct($level, $owner, $scheduler);
        $this->interpreter = new FragGrenadeInterpreter(
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