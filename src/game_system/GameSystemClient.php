<?php


namespace game_system;


use Client;
use game_system\model\Game;
use game_system\service\UsersService;
use game_system\service\WeaponsService;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\item\Item;
use pocketmine\Player;

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

    public function startGame(): bool {
        if ($this->game === null)
            return false;
        $participants = $this->usersService->getParticipants($this->game->getId());


        $this->game->start($participants, function ($winTeam) use ($participants) {
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

        $this->usersService->joinGame($userName, $this->game->getId(), $this->game->getBlueTeam()->getId(), $this->game->getRedTeam()->getId());
        return true;
    }

    public function quitGame(string $userName): void {
        //TODO:アイテム削除とTP
        $this->usersService->quitGame($userName);
    }

    public function onReceivedDamage(Player $attacker, Entity $target, string $weaponName, int $damage) {
        $health = $target->getHealth() - $damage;
        if ($health <= 0 && $target instanceof Human) {
            $target->setHealth(20);

            $players = $attacker->getLevel()->getPlayers();
            foreach ($players as $player) {
                $player->sendMessage($attacker->getName() . " killed " . $target->getName() . " by " . $weaponName);
            }

            $attackerName = $attacker->getName();
            $this->weaponService->addKillCount($attacker, $weaponName);
            $belongTeamId = $this->usersService->getUserData($attackerName)->getBelongTeamId();
            $this->game->onKilledPlayer($belongTeamId);
            $this->usersService->addMoney($attackerName, 100);
        } else {
            $target->setHealth($damage);
        }
    }
}