<?php


namespace game_system\interpreter;


use Closure;
use game_system\GameSystemBinder;
use game_system\model\FragGrenade;
use game_system\pmmp\client\FragGrenadeClient;
use game_system\service\GameScoresService;
use game_system\service\UsersService;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class FragGrenadeInterpreter extends GrenadeBaseInterpreter
{
    public function __construct(Player $owner, UsersService $usersService, GameScoresService $gameScoreService, TaskScheduler $scheduler) {
        parent::__construct($owner, $usersService, $gameScoreService, $scheduler);
        $this->client = new FragGrenadeClient();
        $this->grenade = new FragGrenade();
    }

    public function explode(Vector3 $pos, Closure $onExploded) {
        $func = function () use ($onExploded, $pos) {
            $this->client->explodeParticle($this->owner->getLevel(), $pos);
            $this->client->playSound($this->owner->getLevel(), $pos);
            $onExploded();
        };
        parent::explode($pos, $func);
    }

    public function effectOn(Player $player, int $distance): void {
        var_dump($distance);
        if ($distance <= 3) {
            GameSystemBinder::getInstance()->getGameListener()->onReceivedDamage($this->owner, $player, $this->grenade->getName(), 20);
        } else {
            GameSystemBinder::getInstance()->getGameListener()->onReceivedDamage($this->owner, $player, $this->grenade->getName(), 15 - $distance);
        }
    }
}