<?php


namespace game_system\interpreter;


use game_system\GameSystemBinder;
use game_system\model\FragGrenade;
use game_system\service\GameScoresService;
use game_system\service\UsersService;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class FragGrenadeInterpreter extends GrenadeBaseInterpreter
{
    public function __construct(Player $owner, UsersService $usersService, GameScoresService $gameScoreService, TaskScheduler $scheduler) {
        parent::__construct($owner, $usersService, $gameScoreService, $scheduler);
        $this->grenade = new FragGrenade();
    }

    public function effectOn(Player $player, int $distance): void {
        if ($distance <= 4) {
            GameSystemBinder::getInstance()->getGameListener()->onReceivedDamage($this->owner, $player, $this->grenade->getName(), 20);
        } else if ($distance <= 8) {
            GameSystemBinder::getInstance()->getGameListener()->onReceivedDamage($this->owner, $player, $this->grenade->getName(), 20/$distance);
        }
    }
}