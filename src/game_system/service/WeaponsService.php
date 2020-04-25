<?php


namespace game_system\service;


use game_system\model\User;
use game_system\model\WeaponId;
use Service;

class WeaponsService extends Service
{
    public function updateKillCount(User $owner, WeaponId $weaponId, int $killCount): void { }

    public function updateWinCount(User $owner, WeaponId $weaponId, int $winCount): void { }
}