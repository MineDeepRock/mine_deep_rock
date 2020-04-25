<?php


namespace game_system;


use Client;
use game_system\model\Game;
use game_system\service\UsersService;
use game_system\service\WeaponsService;
use pocketmine\item\Item;
use pocketmine\Server;

class GameSystemClient extends Client
{
    private $usersService;
    private $weaponService;

    private $game;

    public function __construct() {
        $this->usersService = new UsersService();
        $this->weaponService = new WeaponsService();
    }

    public function userLogin(string $userName): void {
        $this->usersService->userLogin($userName);
    }


    public function createGame(Game $game): bool {
        if ($this->game !== null)
            return false;

        $this->game = $game;
        return true;
    }

    public function startGame(Server $server): bool {
        if ($this->game === null)
            return false;
        $participants = $this->usersService->getParticipants($this->game->getId());

        $this->game->start($server, $participants, function ($winTeam) use ($participants) {
            $winTeamId = $winTeam->getId();
            //途中抜けしたプレイヤーを省かないように再取得はしない
            foreach ($participants as $participant) {
                if ($participant->getLastBelongTeamId()->equal($winTeamId))
                    $this->usersService->addWinCount($participant->getName());
            }
        });

        return true;
    }

    public function closeGame(): bool {
        if ($this->game === null)
            return false;
        //TODO
        return true;
    }


    public function joinGame(string $userName): bool {
        if ($this->game === null || $this->game->isStarted())
            return false;

        $this->usersService->joinGame($this->game->getBlueTeam()->getId(), $this->game->getRedTeam()->getId(), $userName);
        return true;
    }

    public function quitGame(string $userName): void {
        $this->usersService->quitGame($userName);
    }

    public function onKilledPlayer(string $attackerName, Item $weapon) {
        if (is_subclass_of($weapon, "gun_system\pmmp\items\ItemGun")) {
            $this->weaponService->addKillCount($attackerName, $weapon->getCustomName());
            $belongTeamId = $this->usersService->getUserData($attackerName)->getBelongTeamId();
            $this->game->onKilledPlayer($belongTeamId);
        }
    }
}