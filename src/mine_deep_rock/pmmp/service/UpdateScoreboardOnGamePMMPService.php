<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\GameTypeList;
use mine_deep_rock\pmmp\scoreboard\DominationScoreboard;
use mine_deep_rock\pmmp\scoreboard\TDMScoreboard;
use mine_deep_rock\store\DominationFlagsStore;
use pocketmine\Server;
use team_game_system\model\GameId;
use team_game_system\TeamGameSystem;

class UpdateScoreboardOnGamePMMPService
{
    static function execute(GameId $gameId): void {
        $playersData = TeamGameSystem::getGamePlayersData($gameId);
        $game = TeamGameSystem::getGame($gameId);

        $server = Server::getInstance();
        if ($game->getType()->equals(GameTypeList::TDM())) {
            foreach ($playersData as $playerData) {
                $player = $server->getPlayer($playerData->getName());
                TDMScoreboard::update($player, $game);
            }
        } else {
            foreach ($playersData as $playerData) {
                $player = $server->getPlayer($playerData->getName());
                DominationScoreboard::update(
                    $player,
                    $game,
                    DominationFlagsStore::findByGameId($gameId));
            }
        }
    }
}