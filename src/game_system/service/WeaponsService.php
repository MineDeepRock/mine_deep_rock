<?php


namespace game_system\service;


use game_system\repository\WeaponsRepository;
use Service;

class WeaponsService extends Service
{
    private $repository;

    public function __construct() {
        $this->repository = new WeaponsRepository();
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
}