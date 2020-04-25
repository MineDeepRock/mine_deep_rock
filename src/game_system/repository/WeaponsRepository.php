<?php


namespace game_system\repository;


use Repository;

class WeaponsRepository extends Repository
{
    public function updateKillCount(string $ownerName, string $weaponId, int $killCount): void { }

    public function updateWinCount(string $ownerName, string $weaponId, int $winCount): void { }
}