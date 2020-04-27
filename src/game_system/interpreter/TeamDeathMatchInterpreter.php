<?php


namespace game_system\interpreter;


use game_system\model\map\RealisticWWIBattlefieldExtended;
use game_system\model\TeamDeathMatch;
use game_system\pmmp\client\TeamDeathMatchClient;
use game_system\service\UsersService;

class TeamDeathMatchInterpreter
{
    private $client;
    private $game;

    private $usersService;

    public function __construct(TeamDeathMatchClient $client, UsersService $userService) {
        $this->client = $client;
        $this->usersService = $userService;
    }

    public function init(): bool {
        if ($this->game !== null)
            return false;

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

    private function spawn(string $userName){
        $user = $this->usersService->getUserData($userName);
        $selectedWeaponName = $user->getSelectedWeaponName();

        $this->client->spawn($userName,$selectedWeaponName,$this->game->getSpawnPoint());
    }

    public function closeGame(): bool {
        if ($this->game === null)
            return false;
        //TODO
        return true;
    }

    public function quitGame(string $userName): void {
        $this->client->quitGame($userName);
        $this->usersService->quitGame($userName);
    }
}