<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\Server;
use team_game_system\model\GameId;
use team_game_system\TeamGameSystem;

class GetPlayersReadyToGamePMMPService
{
    static function execute(GameId $gameId): void {
        $playersData = TeamGameSystem::getGamePlayersData($gameId);
        foreach ($playersData as $playerData) {
            GetPlayerReadyToGamePMMPService::execute($playerData, $gameId);
        }
    }
}