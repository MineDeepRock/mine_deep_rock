<?php


namespace game_system\service;


use game_system\model\GameId;
use game_system\model\User;
use Service;

class UsersService extends Service
{
    public function userLogin(string $userName): void { }

    public function getUserData(string $userName): User { }

    public function getParticipants(GameId $gameId) : array { }

    public function joinGame(string $userName): void { }

    public function quitGame(string $userName): void { }

    public function addWinCount(string $userName): void { }
}