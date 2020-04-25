<?php


namespace game_system;


use Client;
use game_system\model\Game;
use game_system\service\UsersService;

class GameSystemClient extends Client
{
    private $usersService;
    private $holdGameId;

    public function __construct() {
        $this->usersService = new UsersService();
    }

    public function userLogin(string $userName) {
        $this->usersService->userLogin($userName);
    }

    public function createGame(Game $game) {
        $this->holdGameId = $game->getId();
    }

    public function joinGame(string $userName) {
        $this->usersService->joinGame($userName);
    }

    public function quitGame(string $userName) {
        $this->usersService->quitGame($userName);
    }
}