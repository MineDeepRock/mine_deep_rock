<?php


namespace game_system\interpreter;


use game_system\model\TeamDeathMatch;
use game_system\model\User;
use game_system\pmmp\client\TeamDeathMatchClient;
use game_system\service\UsersService;
use game_system\service\WeaponsService;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

class TeamDeathMatchInterpreter
{
    private $client;
    private $usersService;
    private $weaponService;
    private $scheduler;

    private $game;
    private $limitSecond;

    public function __construct(TeamDeathMatchClient $client, UsersService $userService, WeaponsService $weaponService, TaskScheduler $scheduler) {
        $this->client = $client;
        $this->usersService = $userService;
        $this->scheduler = $scheduler;
        $this->weaponService = $weaponService;
    }

    public function init(int $limitSecond): bool {
        if ($this->game !== null)
            return false;

        $this->limitSecond = $limitSecond;
        $this->game = new TeamDeathMatch();
    }

    public function start(): bool {
        if ($this->game === null)
            return false;
        if ($this->game->isStart())//TODO
            return false;

        $participants = $this->usersService->getParticipants($this->game->getId());
        $this->client->start(
            $participants,
            $this->game->getRedTeam()->getId(),
            $this->game->getMap()->getName(),
            $this->game->getRedTeamScore(),
            $this->game->getBlueTeamScore());

        $this->scheduler->scheduleRepeatingTask(new ClosureTask(function (int $tick): void {
            $this->game->pass();
            $this->client->displayRemainingTime($this->limitSecond - $this->game->getElapsedSecond(), $this->game->getMap()->getName());

            if ($this->game->getElapsedSecond() === $this->limitSecond) {
                $participants = $this->usersService->getParticipants($this->game->getId());
                $winTeamId = $this->game->getWinTeam()->getId();
                foreach ($participants as $participant) {
                    if ($participant->getBelongTeamId()->equal($winTeamId)) {
                        $this->usersService->addWinCount($participant->getName());
                        $this->usersService->addMoney($participant->getName(), 1000);
                    }
                }
                $this->client->onFinish($winTeamId, $participants);
            }
        }), 20 * 1);
        return true;
    }

    public function join(string $userName) {
        if ($this->game === null)
            return false;

        $user = $this->usersService->getUserData($userName);
        if ($this->game->isStarted()) {
            if ($user->getLastBelongTeamId()->equal($this->game->getRedTeam()->getId()) ||
                $user->getLastBelongTeamId()->equal($this->game->getBlueTeam()->getId())) {
                $this->usersService->joinGame(
                    $userName,
                    $this->game->getId(),
                    $this->game->getBlueTeam()->getId(),
                    $this->game->getRedTeam()->getId(),
                    $user->getLastBelongTeamId());
                return true;
            }
        } else {
            $this->usersService->joinGame(
                $userName,
                $this->game->getId(),
                $this->game->getBlueTeam()->getId(),
                $this->game->getRedTeam()->getId());
        }
        return true;
    }

    public function onReceiveDamage(Player $attackerPlayer, Entity $targetPlayer, string $weaponName, int $health): void {
        if ($targetPlayer instanceof Human && $targetPlayer->getLevel()->getName() !== $this->game->getMap()->getMap()) {
            $attacker = $this->usersService->getUserData($targetPlayer->getName());
            $target = $this->usersService->getUserData($targetPlayer->getName());

            if (!$attacker->getBelongTeamId()->equal($target->getBelongTeamId())) {
                if ($health <= 0) {
                    $attackerName = $attacker->getName();
                    $this->weaponService->addKillCount($attacker->getName(), $weaponName);
                    $this->usersService->addMoney($attackerName, 100);
                    $this->addScoreByKilling($attacker->getId(), $target);
                    $this->spawn($target);
                }
                $this->client->onReceiveDamage($attackerPlayer, $targetPlayer, $health, $weaponName);
            }
        }
    }

    public function addScoreByKilling(User $attacker, User $victim): void {
        $attackerTeamId = $attacker->getBelongTeamId();

        if ($attackerTeamId->equal($this->game->getRedTeam()->getId())) {
            $this->game->redTeamScore++;
            $this->client->updateRedTeamScoreboard($this->game->redTeamScore, $this->game->getMap()->getName());
        } else {
            $this->game->blueTeamScore++;
            $this->client->updateBlueTeamScoreboard($this->game->blueTeamScore, $this->game->getMap()->getName());
        }
        $this->spawn($victim);
    }

    private function spawn(User $user): void {
        $selectedWeaponName = $user->getSelectedWeaponName();

        $this->client->spawn($user->getName(), $selectedWeaponName, $this->game->getSpawnPoint());
    }

    public function closeGame(): bool {
        if ($this->game === null)
            return false;
        //TODO
        return true;
    }

    public function quitGame(string $userName): bool {
        if ($this->game === null)
            return false;

        $this->client->quitGame($userName);
        $this->usersService->quitGame($userName);
        return true;
    }
}