<?php


namespace game_system\service;


use game_system\model\Weapon;
use game_system\repository\WeaponsRepository;
use Service;

class WeaponsService extends Service
{
    private $repository;

    public function __construct() {
        $this->repository = new WeaponsRepository();
    }

    public function isOwn(string $ownerName, string $weaponName) {
        $ownWeapons = $this->getOwnWeapons($ownerName);
        return in_array($weaponName, $ownWeapons);
    }

    public function getWeapon(string $ownerName,string $weaponName): Weapon {
        return $this->repository->getWeapon($ownerName,$weaponName);
    }

    public function getOwnWeapons(string $ownerName): array {
        return $this->repository->getOwnWeapons($ownerName);
    }

    public function register(string $ownerName, string $weaponName): void {
        $this->repository->register($ownerName, $weaponName);
    }

    public function addKillCount(string $ownerName, string $weaponName): void {
        $this->repository->addKillCount($ownerName, $weaponName);
    }

    public function setScope(string $ownerName, string $weaponName, string $scopeName): void {
        $this->repository->setScope($ownerName, $weaponName,$scopeName);
    }
}