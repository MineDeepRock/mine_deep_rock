<?php


namespace game_system\interpreter;


use Closure;
use game_system\model\Coordinate;
use game_system\model\Grenade;
use game_system\model\military_department\AssaultSoldier;
use game_system\model\military_department\Scout;
use game_system\pmmp\items\FragGrenadeItem;
use game_system\pmmp\items\SpawnFlareBoxItem;
use game_system\service\GameScoresService;
use game_system\service\UsersService;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

abstract class GrenadeBaseInterpreter
{
    protected $client;
    protected $usersService;
    protected $gameScoreService;
    protected $scheduler;


    protected $owner;
    protected $ownerTeamId;
    /**
     * @var Grenade
     */
    protected $grenade;

    public function __construct(Player $owner, UsersService $usersService, GameScoresService $gameScoreService, TaskScheduler $scheduler) {
        $this->owner = $owner;
        $this->usersService = $usersService;
        $this->gameScoreService = $gameScoreService;
        $this->scheduler = $scheduler;

        $this->ownerTeamId = $this->usersService->getUserData($owner->getName())->getBelongTeamId();
    }

    public function explode(Vector3 $pos, Closure $onExploded) {
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($pos, $onExploded): void {
            $players = $this->getAroundEnemyPlayers($pos);
            foreach ($players as $player) {
                $this->effectOn($player,$pos->distance($player->getPosition()));
            }
            $onExploded();
        }), 20 * $this->grenade->getDelay());
    }

    public function getAroundEnemyPlayers(Vector3 $pos): array {
        if ($this->owner->getLevel() === null) {
            return [];
        }
        $players = $this->owner->getLevel()->getPlayers();
        return array_filter($players, function ($player) use ($pos) {
            $belongTeamId = $this->usersService->getUserData($player->getName())->getBelongTeamId();
            if ($this->ownerTeamId === null) return false;
            if ($belongTeamId === null) return false;
            if ($this->ownerTeamId->equal($belongTeamId)) return false;

            return $pos->distance($player->getPosition()) <= $this->grenade->getRange();
        });
    }

    abstract function effectOn(Player $player, int $distance): void;

    public function giveAgain(): void {
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick): void {
            if (!$this->owner->isOnline()) return;
            if ($this->owner->getGamemode() !== Player::ADVENTURE) return;

            $user = $this->usersService->getUserData($this->owner->getName());
            $assaultSoldier = new AssaultSoldier();
            if ($user->getMilitaryDepartment()->getName() !== $assaultSoldier->getName()) return;

            $contain = $this->owner->getInventory()->contains(new FragGrenadeItem());
            if ($contain) return;

            $this->owner->getInventory()->addItem(new FragGrenadeItem());
        }), 20 * 10);
    }
}