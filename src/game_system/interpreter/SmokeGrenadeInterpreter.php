<?php


namespace game_system\interpreter;


use Closure;
use game_system\model\military_department\NursingSoldier;
use game_system\model\military_department\Scout;
use game_system\model\SmokeGrenade;
use game_system\pmmp\client\SmokeGrenadeClient;
use game_system\pmmp\items\SmokeGrenadeItem;
use game_system\service\GameScoresService;
use game_system\service\UsersService;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

class SmokeGrenadeInterpreter extends GrenadeBaseInterpreter
{
    private $handler;

    public function __construct(Player $owner, UsersService $usersService, GameScoresService $gameScoreService, TaskScheduler $scheduler) {
        parent::__construct($owner, $usersService, $gameScoreService, $scheduler);
        $this->client = new SmokeGrenadeClient();
        $this->grenade = new SmokeGrenade();
    }

    public function stop() {
        if ($this->handler !== null) {
            $this->handler->cancel();
        }
    }

    public function explode(Vector3 $pos, Closure $onExploded) {
        $level = $this->owner->getLevel();
        $this->handler = $this->scheduler->scheduleDelayedRepeatingTask(new ClosureTask(function (int $tick) use ($level, $pos, $onExploded): void {
            if ($this->owner->isOnline()) {
                for ($i = 0; $i < 15; ++$i) {
                    $this->client->explodeParticle($level, new Vector3(
                        $pos->getX() + rand(-1, 1),
                        $pos->getY(),
                        $pos->getZ() + rand(-1, 1)
                    ));
                }
            }
        }), 20 * SmokeGrenade::DELAY, 20 * 0.5);
    }

    public function effectOn(Player $player, int $distance): void { }

    public function giveAgain(): void {
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick): void {
            if (!$this->owner->isOnline()) return;
            if ($this->owner->getGamemode() !== Player::ADVENTURE) return;

            $user = $this->usersService->getUserData($this->owner->getName());
            $nursingSoldier = new NursingSoldier();
            $scout = new Scout();
            if ($user->getMilitaryDepartment()->getName() !== $nursingSoldier->getName() &&
                $user->getMilitaryDepartment()->getName() !== $scout->getName()) return;

            $contain = $this->owner->getInventory()->contains(new SmokeGrenadeItem());
            if ($contain) return;

            $this->owner->getInventory()->addItem(new SmokeGrenadeItem());
        }), 20 * 15);
    }
}