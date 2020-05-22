<?php


namespace game_system\interpreter;


use game_system\model\military_department\Engineer;
use game_system\pmmp\client\AmmoBoxClient;
use game_system\pmmp\Entity\AmmoBoxEntity;
use game_system\pmmp\items\SpawnAmmoBoxItem;
use game_system\service\GameScoresService;
use game_system\service\UsersService;
use game_system\service\WeaponsService;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

class AmmoBoxInterpreter
{
    private $client;
    private $usersService;
    private $weaponService;
    private $gameScoresService;
    private $scheduler;
    private $handler;

    private $owner;
    private $ownerTeamId;
    private $gameId;

    function __construct(
        Player $player,
        UsersService $usersService,
        WeaponsService $weaponService,
        GameScoresService $gameScoresService,
        TaskScheduler $scheduler) {
        $this->usersService = $usersService;
        $this->weaponService = $weaponService;
        $this->gameScoresService = $gameScoresService;
        $this->client = new AmmoBoxClient();
        $this->scheduler = $scheduler;

        $this->owner = $player;
        $user = $this->usersService->getUserData($player->getName());
        $this->gameId = $user->getParticipatedGameId();
        $this->ownerTeamId = $user->getBelongTeamId();
    }

    public function carryOut(AmmoBoxEntity $ammoBoxEntity) {
        $this->handler = $this->scheduler->scheduleDelayedRepeatingTask(new ClosureTask(function (int $tick) use ($ammoBoxEntity): void {
            if (!$this->owner->isOnline()) {
                $this->stop();
            } else {
                $this->client->summonParticle(
                    $this->owner->getLevel(),
                    $ammoBoxEntity->getPosition());
                foreach ($this->getAroundTeamPlayers($ammoBoxEntity->getPosition()) as $player) {
                    $this->client->useAmmoBox(
                        $this->owner,
                        $player,
                        function () {
                            $this->gameScoresService->addPoint($this->owner->getName(), $this->gameId, 2);
                        });
                }
            }
        }), 20 * 2, 20 * 5);
    }

    public function stop(): void {
        $this->handler->cancel();
        $this->giveAgain();
    }


    private function getAroundTeamPlayers(Vector3 $pos): array {
        if ($this->owner->getLevel() === null) {
            return [];
        }
        $players = $this->owner->getLevel()->getPlayers();
        return array_filter($players, function ($player) use ($pos) {
            $belongTeamId = $this->usersService->getUserData($player->getName())->getBelongTeamId();
            if ($this->ownerTeamId === null) return false;
            if ($belongTeamId === null) return false;
            if (!$this->ownerTeamId->equal($belongTeamId)) return false;

            return $pos->distance($player->getPosition()) < 6;
        });
    }

    public function giveAgain(): void {
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick): void {
            if (!$this->owner->isOnline()) return;
            if ($this->owner->getGamemode() !== Player::ADVENTURE) return;

            $user = $this->usersService->getUserData($this->owner->getName());
            $engineer = new Engineer();
            if ($user->getMilitaryDepartment()->getName() !== $engineer->getName()) return;

            $contain = $this->owner->getInventory()->contains(new SpawnAmmoBoxItem());
            if ($contain) return;

            $this->owner->getInventory()->addItem(new SpawnAmmoBoxItem());
        }), 20 * 10);
    }
}