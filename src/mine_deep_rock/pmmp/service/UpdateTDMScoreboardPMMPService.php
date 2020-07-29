<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\pmmp\scoreboard\TDMScoreboard;
use pocketmine\Server;
use team_game_system\model\GameId;
use team_game_system\TeamGameSystem;

class UpdateTDMScoreboardPMMPService
{
    static function execute(GameId $gameId): void {
        $playersData = TeamGameSystem::getGamePlayersData($gameId);
        $game = TeamGameSystem::getGame($gameId);

        $redTeam = $game->getTeams()[0];
        $blueTeam = $game->getTeams()[1];

        foreach ($playersData as $playerData) {
            $player = Server::getInstance()->getPlayer($playerData->getName());
            TDMScoreboard::update($player, $game->getMap()->getName(), $redTeam->getScore()->getValue(), $blueTeam->getScore()->getValue());
        }
    }
}