<?php


namespace game_system;


use Client;
use easy_scoreboard_api\EasyScoreboardAPI;
use game_system\model\Game;
use game_system\pmmp\WeaponSelectForm;
use game_system\pmmp\WorldController;
use game_system\service\UsersService;
use game_system\service\WeaponsService;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\Player;
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

    public function selectWeapon(Player $player) {
        $playerName = $player->getName();
        $player->sendForm(new WeaponSelectForm(function ($weaponName) use ($playerName) {
            if ($weaponName !== null) {
                //if ($this->weaponService->isOwn($playerName, $weaponName)) {
                    $this->usersService->selectWeapon($playerName, $weaponName);
                //}
            }

        }));
    }

    public function userLogin(string $userName): void {
        $player = Server::getInstance()->getPlayer($userName);
        $player->getInventory()->setContents([]);
        $worldController = new WorldController();
        $worldController->teleport($player, "world");

        if (!$this->usersService->exists($userName))
            $this->weaponService->register($userName, "M1907SL");

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
            $worldController = new WorldController();
            $winTeamId = $winTeam->getId();

            $participants = $this->usersService->getParticipants($this->game->getId());
            foreach ($participants as $participant) {
                EasyScoreboardAPI::getInstance()->allremove($participant->getName());
                $player = Server::getInstance()->getPlayer($participant->getName());
                $player->getInventory()->setContents([]);
                $worldController->teleport($player, "world");

                if ($participant->getBelongTeamId()->equal($winTeamId)) {
                    $player->addTitle("勝利!!");
                    $this->usersService->addWinCount($participant->getName());
                    $this->usersService->addMoney($participant->getName(), 1000);
                } else {
                    $player->addTitle("負け");
                }
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
        if ($this->game === null)
            return false;

        if ($this->game->isStarted()) {
            $this->joinGAmeOnTheWay($userName);
        } else {
            $this->usersService->joinGame(
                $userName,
                $this->game->getId(),
                $this->game->getBlueTeam()->getId(),
                $this->game->getRedTeam()->getId());
        }
        return true;
    }

    public function joinGameOnTheWay(string $userName): bool {
        $user = $this->usersService->getUserData($userName);
        if ($user->getLastBelongTeamId() === $this->game->getRedTeam()->getId() || $user->getLastBelongTeamId() === $this->game->getBlueTeam()->getId()) {
            $this->usersService->joinGame(
                $userName,
                $this->game->getId(),
                $this->game->getBlueTeam()->getId(),
                $this->game->getRedTeam()->getId(),
                $user->getLastBelongTeamId());
            return true;
        }
        $this->usersService->joinGame(
            $userName,
            $this->game->getId(),
            $this->game->getBlueTeam()->getId(),
            $this->game->getRedTeam()->getId());

        $this->game->joinGameOnTheWay($user);
        return true;
    }

    public function quitGame(string $userName): void {
        $player = Server::getInstance()->getPlayer($userName);
        if ($player->isOnline()) {
            $player->getInventory()->setContents([]);
            $worldController = new WorldController();
            $worldController->teleport($player, "world");
            EasyScoreboardAPI::getInstance()->allremove($userName);
        }

        $this->usersService->quitGame($userName);
    }

    public function onReceivedDamage(Player $attacker, Entity $target, string $weaponName, int $damage): bool {
        $health = $target->getHealth() - $damage;

        //撃たれた相手が人間じゃない
        if (!($target instanceof Human)) {
            $target->setHealth($health);
            return true;
        }

        if ($target instanceof Human && $target->getLevel()->getName() !== "world") {

            $attackerTeamId = $this->usersService->getUserData($attacker->getName())->getBelongTeamId();
            $targetTeamId = $this->usersService->getUserData($target->getName())->getBelongTeamId();
            if ($attackerTeamId->equal($targetTeamId)) {
                return false;
            }

            if ($health <= 0) {
                $target->setHealth(20);

                $players = $attacker->getLevel()->getPlayers();
                foreach ($players as $player) {
                    $player->sendMessage($attacker->getName() . " killed " . $target->getName() . " by " . $weaponName);
                }

                $attackerName = $attacker->getName();

                $this->weaponService->addKillCount($attacker->getName(), $weaponName);

                $attackerTeamId = $this->usersService->getUserData($attackerName)->getBelongTeamId();

                $user = $this->usersService->getUserData($target->getName());
                $this->game->onKilledPlayer($attackerTeamId, $user);
                $this->usersService->addMoney($attackerName, 100);
                return true;
            }
            $target->setHealth($health);
            return true;
        }
        return true;
    }
}