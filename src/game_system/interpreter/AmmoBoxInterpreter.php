<?php


namespace game_system\interpreter;


use game_system\model\AmmoBox;
use game_system\model\Coordinate;
use game_system\model\military_department\AssaultSoldier;
use game_system\model\military_department\Scout;
use game_system\pmmp\client\AmmoBoxClient;
use game_system\pmmp\items\SpawnAmmoBoxItem;
use game_system\service\GameScoresService;
use game_system\service\UsersService;
use game_system\service\WeaponsService;
use gun_system\models\GunList;
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
    private $ammoBox;

    function __construct(
        Player $player,
        Coordinate $coordinate,
        UsersService $usersService,
        WeaponsService $weaponService,
        GameScoresService $gameScoresService,
        TaskScheduler $scheduler) {
        $this->usersService = $usersService;
        $this->weaponService = $weaponService;
        $this->gameScoresService = $gameScoresService;
        $this->client = new AmmoBoxClient();
        $this->scheduler = $scheduler;

        $this->ammoBox = new AmmoBox(40, $coordinate);
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
                        $this->ammoBox->getCoordinate()->getX(),
                        $this->ammoBox->getCoordinate()->getY(),
                        $this->ammoBox->getCoordinate()->getZ()));
                foreach ($this->getAroundTeamPlayers() as $player) {
                    $user = $this->usersService->getUserData($player->getName());
                    $gun = GunList::fromString($user->getSelectedWeaponName());
                    $subGun = GunList::fromString($user->getSelectedSubWeaponName());
                    $this->gameScoresService->addPoint($this->owner->getName(), $gameId, 2);
                    //TODO:武器ごとにかえる
                    $this->client->useAmmoBox(
                        $this->owner,
                        $player,
                        $gun->getType(),
                        10);
                    $this->client->useAmmoBox(
                        $this->owner,
                        $player,
                        $subGun->getType(),
                        5);
                }
            }
        }), 20 * 2, 20 * 5);
    }

    public function stop(): void {
        $this->handler->cancel();
        $this->giveAgain();
    }

    public function getAmmoBox(): AmmoBox {
        return $this->ammoBox;
    }

    private function getAroundTeamPlayers(): array {
        if ($this->owner->getLevel() === null) {
            return [];
        }
        $players = $this->owner->getLevel()->getPlayers();
        return array_filter($players, function ($player) {
            $belongTeamId = $this->usersService->getUserData($player->getName())->getBelongTeamId();
            if ($this->ownerTeamId === null) return false;
            if ($belongTeamId === null) return false;
            if (!$this->ownerTeamId->equal($belongTeamId)) return false;
            $ammoPosition = new Vector3(
                $this->ammoBox->getCoordinate()->getX(),
                $this->ammoBox->getCoordinate()->getY(),
                $this->ammoBox->getCoordinate()->getZ()
            );

            return $ammoPosition->distance($player->getPosition()) < 6;
        });
    }

    public function giveAgain(): void {
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick): void {
            if (!$this->owner->isOnline()) return;
            if ($this->owner->getGamemode() !== Player::ADVENTURE) return;

            $user = $this->usersService->getUserData($this->owner->getName());
            $scout = new Scout();
            if ($user->getMilitaryDepartment()->getName() !== $scout->getName()) return;

            $contain = $this->owner->getInventory()->contains(new SpawnAmmoBoxItem());
            if ($contain) return;

            $this->owner->getInventory()->addItem(new SpawnAmmoBoxItem());
        }), 20 * 10);
    }
}