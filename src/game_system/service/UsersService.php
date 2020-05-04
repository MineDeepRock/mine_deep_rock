<?php


namespace game_system\service;


use game_system\model\GameId;
use game_system\model\TeamId;
use game_system\model\User;
use game_system\repository\UsersRepository;
use Service;

class UsersService extends Service
{
    private $repository;

    public function __construct() {
        $this->repository = new UsersRepository();
    }

    public function exists(string $userName): bool {
        return $this->repository->exists($userName);
    }

    public function userLogin(string $userName): void {
        $this->repository->userLogin($userName);
    }

    public function getUserData(string $userName): User {
        return $this->repository->getUserData($userName);
    }

    public function getParticipants(GameId $gameId): array {
        return $this->repository->getParticipants($gameId->value());
    }

    public function selectMilitaryDepartment(string $userName, string $militaryDepartmentName): void {
        $this->repository->selectMilitaryDepartment($userName, $militaryDepartmentName);
    }

    public function selectWeapon(string $userName, string $weaponName): void {
        $this->repository->selectWeapon($userName, $weaponName);
    }

    public function selectSubWeapon(string $userName, string $weaponName): void {
        $this->repository->selectSubWeapon($userName, $weaponName);
    }

    public function joinGame(string $userName, GameId $gameId, TeamId $redTeamId, TeamId $blueTeamId, ?TeamId $joinTeamId = null): TeamId {
        if ($joinTeamId !== null) {
            $this->repository->joinTeam($userName, $joinTeamId->value(), $gameId->value());
            return $joinTeamId;
        }

        $numberOfRedTeamMember = 0;
        $numberOfBlueTeamMember = 0;
        $gameParticipants = $this->getParticipants($gameId);
        foreach ($gameParticipants as $participant) {
            if ($redTeamId->equal($participant->getBelongTeamId())) {
                $numberOfRedTeamMember++;
            } else {
                $numberOfBlueTeamMember++;
            }
        }

        if ($numberOfRedTeamMember > $numberOfBlueTeamMember) {
            $this->repository->joinTeam($userName, $blueTeamId->value(), $gameId->value());
            return $blueTeamId;
        } else {
            $this->repository->joinTeam($userName, $redTeamId->value(), $gameId->value());
            return $redTeamId;
        }
    }

    public function quitGame(string $userName): void {
        $this->repository->quitTeam($userName);
    }

    public function addWinCount(string $userName): void {
        $this->repository->addWinCount($userName);
    }

    public function addMoney(string $userName, int $value): void {
        $this->repository->addMoney($userName, $value);
    }

    public function spendMoney(string $userName, int $value): void {
        $this->repository->spendMoney($userName, $value);
    }
}