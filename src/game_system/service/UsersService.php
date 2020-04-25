<?php


namespace game_system\service;


use game_system\model\TeamId;
use Service;

class UsersService extends Service
{
    public function userRegister(string $userName) {}

    public function getUserData(string $userName) {}

    public function joinTeam(TeamId $teamId) {}

    public function quitTeam(TeamId $teamId) {}
}