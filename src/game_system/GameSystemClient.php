<?php


namespace game_system;


use Client;
use game_system\model\Game;
use game_system\service\UsersService;
use game_system\service\WeaponsService;
use pocketmine\item\Item;

class GameSystemClient extends Client
{
    private $usersService;
    private $weaponService;

    private $holdGameId;

    public function __construct() {
        $this->usersService = new UsersService();
        $this->weaponService = new WeaponsService();
    }

    public function userLogin(string $userName): void {
        $this->usersService->userLogin($userName);
    }


    public function createGame(Game $game): bool {
        if ($this->holdGameId !== null)
            return false;

        $this->holdGameId = $game->getId();
        return true;
    }

    public function closeGame(): bool {
        if ($this->holdGameId === null)
            return false;
        return true;
    }


    public function joinGame(string $userName): bool {
        if ($this->holdGameId === null)
            return false;

        $this->usersService->joinGame($userName);
        return true;
    }

    public function quitGame(string $userName): void {
        $this->usersService->quitGame($userName);
    }

    public function onKilledPlayer(string $attackerName, Item $weapon) {
        if (is_subclass_of($weapon, "gun_system\pmmp\items\ItemGun")) {
            $this->weaponService->addKillCount($attackerName, $weapon->getCustomName());
        }
    }
}