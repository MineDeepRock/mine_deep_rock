<?php


namespace game_system\interpreter;


use game_system\model\AmmoBox;
use game_system\model\Coordinate;
use game_system\model\TeamId;
use game_system\pmmp\client\AmmoBoxClient;
use game_system\service\UsersService;
use game_system\service\WeaponsService;
use gun_system\models\GunList;
use gun_system\models\GunType;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;

class AmmoBoxInterpreter
{
    private $client;
    private $usersService;
    private $weaponService;
    private $scheduler;

    private $owner;
    private $ownerTeamId;
    private $ammoBox;

    function __construct(
        Player $player,
        Coordinate $coordinate,
        UsersService $usersService,
        WeaponsService $weaponService,
        TaskScheduler $scheduler) {
        $this->usersService = $usersService;
        $this->weaponService = $weaponService;
        $this->client = new AmmoBoxClient();
        $this->scheduler = $scheduler;

        $this->ammoBox = new AmmoBox(40,$coordinate);
        $this->owner = $player;
        $this->ownerTeamId = $this->usersService->getUserData($player->getName())->getBelongTeamId();

        $this->scheduler->scheduleDelayedRepeatingTask(new ClosureTask(function (int $tick): void {
            foreach ($this->getAroundTeamPlayers() as $player) {
                $user = $this->usersService->getUserData($player->getName());
                $gun = GunList::fromString($user->getSelectedWeaponName());
                $subGun = GunList::fromString($user->getSelectedSubWeaponName());
                //TODO:武器ごとにかえる
                $this->client->useAmmoBox(
                    $player,
                    $gun->getType(),
                    20);
                $this->client->useAmmoBox(
                    $player,
                    $subGun->getType(),
                    5);
            }
        }), 20 * 2, 20 * 5);
    }

    public function getAmmoBox(): AmmoBox {
        return $this->ammoBox;
    }

    private function getAroundTeamPlayers(): array {
        $players = $this->owner->getLevel()->getPlayers();
        return array_filter($players, function ($player) {
            $belongTeamId = $this->usersService->getUserData($player->getName())->getBelongTeamId();
            if ($belongTeamId === null) return false;
            if (!$this->ownerTeamId->equal($belongTeamId)) return false;
            $ammoPosition = new Vector3(
                $this->ammoBox->getCoordinate()->getX(),
                $this->ammoBox->getCoordinate()->getY(),
                $this->ammoBox->getCoordinate()->getZ()
            );

            return $ammoPosition->distance($player->getPosition()) < 15;
        });
    }
}