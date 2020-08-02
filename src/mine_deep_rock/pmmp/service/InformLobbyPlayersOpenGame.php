<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\Server;

class InformLobbyPlayersOpenGame
{
    static function execute(string $gameType) {
        $level = Server::getInstance()->getLevelByName("lobby");
        foreach ($level->getPlayers() as $player) {
            $player->sendMessage("{$gameType}が開かれました");
        }
    }
}