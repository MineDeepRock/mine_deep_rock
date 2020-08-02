<?php


namespace mine_deep_rock\pmmp\service;


use team_game_system\model\GameId;
use team_game_system\TeamGameSystem;

class GetPlayersReadyToTDMPMMPService
{
    static function execute(GameId $gameId): void {
        $playersData = TeamGameSystem::getGamePlayersData($gameId);
        foreach ($playersData as $playerData) {
            GetPlayerReadyToTDMPMMPService::execute($playerData, $gameId);
        }
    }
}