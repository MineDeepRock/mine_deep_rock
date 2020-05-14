<?php


namespace game_system\interpreter;


use game_system\model\Coordinate;
use game_system\model\FlareBox;
use game_system\model\military_department\Scout;
use game_system\pmmp\client\FlareBoxClient;
use game_system\pmmp\items\SpawnFlareBoxItem;
use game_system\service\GameScoresService;
use game_system\service\UsersService;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

class FlareBoxInterpreter
{
    private $client;
    private $usersService;
    private $gameScoresService;
    private $scheduler;
    private $handler;

    private $owner;
    private $ownerTeamId;
    private $flareBox;

    function __construct(
        Coordinate $coordinate,
        Player $player,
        UsersService $usersService,
        GameScoresService $gameScoresService,
        TaskScheduler $scheduler) {
        $this->usersService = $usersService;
        $this->gameScoresService = $gameScoresService;
        $this->client = new FlareBoxClient();
        $this->scheduler = $scheduler;

        $this->flareBox = new FlareBox($coordinate);
        $this->owner = $player;
        $user = $this->usersService->getUserData($player->getName());
        $gameId = $user->getParticipatedGameId();
        $this->ownerTeamId = $user->getBelongTeamId();

        $this->handler = $this->scheduler->scheduleDelayedRepeatingTask(new ClosureTask(function (int $tick) use ($gameId): void {
            if (!$this->owner->isOnline()) {
                $this->stop();
            } else {
                $this->client->summonParticle(
                    $this->owner->getLevel(),
                    new Vector3(
                        $this->flareBox->getCoordinate()->getX(),
                        $this->flareBox->getCoordinate()->getY(),
                        $this->flareBox->getCoordinate()->getZ()));
                foreach ($this->getAroundEnemyPlayers() as $player) {
                    $this->gameScoresService->addPoint($this->owner->getName(), $gameId, 2);
                    $this->effectOn($player);
                }
            }
        }), 20 * 2, 20 * 5);
    }

    public function effectOn(Player $player): void {
        $this->client->effectOn($this->owner, $player);
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($player): void {
            if ($player->isOnline()) {
                $this->release($player);
            }
        }), 20 * 4);
    }

    public function release(Player $player): void {
        $this->client->release($player);
    }

    public function stop(): void {
        $this->handler->cancel();
        $this->giveAgain();
    }

    private function getAroundEnemyPlayers(): array {
        if ($this->owner->getLevel() === null) {
            return [];
        }
        $players = $this->owner->getLevel()->getPlayers();
        return array_filter($players, function ($player) {
            $belongTeamId = $this->usersService->getUserData($player->getName())->getBelongTeamId();
            if ($this->ownerTeamId === null) return false;
            if ($belongTeamId === null) return false;
            if ($this->ownerTeamId->equal($belongTeamId)) return false;
            $flarePosition = new Vector3(
                $this->flareBox->getCoordinate()->getX(),
                $this->flareBox->getCoordinate()->getY(),
                $this->flareBox->getCoordinate()->getZ()
            );

            return $flarePosition->distance($player->getPosition()) <= 25;
        });
    }

    public function giveAgain(): void {
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick): void {
            if (!$this->owner->isOnline()) return;
            if ($this->owner->getGamemode() !== Player::ADVENTURE) return;

            $user = $this->usersService->getUserData($this->owner->getName());
            $scout = new Scout();
            if ($user->getMilitaryDepartment()->getName() !== $scout->getName()) return;

            $contain = $this->owner->getInventory()->contains(new SpawnFlareBoxItem());
            if ($contain) return;

            $this->owner->getInventory()->addItem(new SpawnFlareBoxItem());
        }), 20 * 10);
    }

    public function getFlareBox(): FlareBox {
        return $this->flareBox;
    }
}