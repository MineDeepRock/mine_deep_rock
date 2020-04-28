<?php


namespace game_system\interpreter;


use game_system\model\map\TeamDeathMatchMap;
use game_system\model\TeamDeathMatch;
use game_system\model\User;
use game_system\pmmp\client\TeamDeathMatchClient;
use game_system\service\UsersService;
use game_system\service\WeaponsService;
use pocketmine\entity\Entity;
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

    public function init(TeamDeathMatchMap $map, int $limitSecond): bool {
        if ($this->game !== null)
            return false;

        $this->limitSecond = $limitSecond;
        $this->game = new TeamDeathMatch($map);
        return true;
    }

    public function start(): bool {
        if ($this->game === null)
            return false;
        if ($this->game->isStarted())
            return false;

        $participants = $this->usersService->getParticipants($this->game->getId());
        $this->client->start(
            $participants,
            $this->game->getRedTeam()->getId(),
            $this->game->redTeamScore,
            $this->game->blueTeamScore);


        $participants = $this->usersService->getParticipants($this->game->getId());
        foreach ($participants as $participant) {
            $this->spawn($participant);
        }

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

    public function join(string $userName): bool {
        if ($this->game === null)
            return false;

        $user = $this->usersService->getUserData($userName);
        if ($user->getParticipatedGameId() !== null)
            return false;

        if ($this->game->isStarted()) {
            if ($user->getLastBelongTeamId()->equal($this->game->getRedTeam()->getId()) ||
                $user->getLastBelongTeamId()->equal($this->game->getBlueTeam()->getId())) {
                $this->usersService->joinGame(
                    $userName,
                    $this->game->getId(),
                    $this->game->getBlueTeam()->getId(),
                    $this->game->getRedTeam()->getId(),
                    $user->getLastBelongTeamId());

                $this->client->joinOnTheWay(
                    $user,
                    $this->game->getRedTeam()->getId(),
                    $this->game->redTeamScore,
                    $this->game->blueTeamScore);

                $this->spawn($user);
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

    public function onReceiveDamage(Player $attackerPlayer, Entity $targetPlayer, string $weaponName, int $damage): void {
        $health = $targetPlayer->getHealth() - $damage;
        if ($this->game !== null) {
            if ($targetPlayer->getLevel()->getName() === $this->game->getMap()->getName()) {
                $attacker = $this->usersService->getUserData($attackerPlayer->getName());
                $target = $this->usersService->getUserData($targetPlayer->getName());

                if (!($attacker->getBelongTeamId()->equal($target->getBelongTeamId()))) {
                    $this->client->onReceiveDamage($attackerPlayer, $targetPlayer, $damage, $weaponName);

                    if ($health <= 0) {
                        $attackerName = $attacker->getName();
                        $this->weaponService->addKillCount($attacker->getName(), $weaponName);
                        $this->usersService->addMoney($attackerName, 100);
                        $this->addScoreByKilling($attacker, $target);
                        $this->spawn($target);
                    }
                }
            }
        }
    }

    public function addScoreByKilling(User $attacker, User $victim): void {
        $attackerTeamId = $attacker->getBelongTeamId();

        if ($attackerTeamId->equal($this->game->getRedTeam()->getId())) {
            $this->client->updateRedTeamScoreboard(++$this->game->redTeamScore, $this->game->getMap()->getName());
        } else {
            $this->client->updateBlueTeamScoreboard(++$this->game->blueTeamScore, $this->game->getMap()->getName());
        }
        $this->spawn($victim);
    }

    private function spawn(User $user): void {
        $selectedWeaponName = $user->getSelectedWeaponName();
        $mapName = $this->game->getMap()->getName();

        $this->client->spawn($user->getName(), $selectedWeaponName, $mapName, $this->game->getSpawnPoint($user->getBelongTeamId()));
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