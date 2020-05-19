<?php


namespace game_system\interpreter;


use Closure;
use game_system\GameSystemBinder;
use game_system\model\FlameBottle;
use game_system\model\military_department\Engineer;
use game_system\pmmp\client\FlameBottleClient;
use game_system\pmmp\Entity\GrenadeEntity;
use game_system\pmmp\items\FlameBottleItem;
use game_system\service\GameScoresService;
use game_system\service\UsersService;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

class FlameBottleInterpreter extends GrenadeBaseInterpreter
{
    private $handler;

    public function __construct(Player $owner, UsersService $usersService, GameScoresService $gameScoreService, TaskScheduler $scheduler) {
        parent::__construct($owner, $usersService, $gameScoreService, $scheduler);
        $this->client = new FlameBottleClient();
        $this->grenade = new FlameBottle();
    }

    public function stop() {
        if ($this->handler) {
            $this->handler->cancel();
        }
    }

    public function explode(GrenadeEntity $entity, Closure $onExploded) {
        $level = $this->owner->getLevel();
        $this->handler = $this->scheduler->scheduleDelayedRepeatingTask(new ClosureTask(function (int $tick) use ($level, $entity, $onExploded): void {
            if ($this->owner->isOnline()) {
                for ($i = 0; $i < 15; ++$i) {
                    $this->client->explodeParticle($level, new Vector3(
                        $entity->getX() + rand(-FlameBottle::RANGE, FlameBottle::RANGE),
                        $entity->getY() + rand(0,2),
                        $entity->getZ() + rand(-FlameBottle::RANGE, FlameBottle::RANGE)
                    ));
                }
                parent::explode($entity, function(){});
            }
        }), 20 * FlameBottle::DELAY, 20 * 0.5);
    }

    public function effectOn(Player $player, int $distance): void {
        GameSystemBinder::getInstance()->getGameListener()->onReceivedDamage($this->owner, $player, FlameBottle::NAME, 4);
    }

    public function giveAgain(): void {
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick): void {
            if (!$this->owner->isOnline()) return;
            if ($this->owner->getGamemode() !== Player::ADVENTURE) return;

            $user = $this->usersService->getUserData($this->owner->getName());
            $engineer = new Engineer();
            if ($user->getMilitaryDepartment()->getName() !== $engineer->getName()) return;

            $contain = $this->owner->getInventory()->contains(new FlameBottleItem());
            if ($contain) return;

            $this->owner->getInventory()->addItem(new FlameBottleItem());
        }), 20 * 15);
    }
}