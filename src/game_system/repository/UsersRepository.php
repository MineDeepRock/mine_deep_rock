<?php


namespace game_system\repository;


use Repository;

class UsersRepository extends Repository
{
    public function userRegister(string $userName) {}

    public function getUserData(string $userName) {}

    public function joinTeam(string $teamId) {}

    public function quitTeam(string $teamId) {}
}