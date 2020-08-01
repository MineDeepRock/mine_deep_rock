<?php


namespace mine_deep_rock\pmmp\service;


use bossbar_system\models\BossBar;
use mine_deep_rock\pmmp\scoreboard\TDMScoreboard;
use pocketmine\Server;
use team_game_system\model\GameId;
use team_game_system\TeamGameSystem;

//TODO:英語変、具体的すぎ
class GetPlayersReadyToTDM
{
    static function execute(GameId $gameId):void {
        $game = TeamGameSystem::getGame($gameId);

        $bossBar = new BossBar("残り時間:" . ($game->getTimeLimit() - $game->getElapsedTime()), $game->getElapsedTime() / $game->getTimeLimit());

        $playersData = TeamGameSystem::getGamePlayersData($gameId);
        foreach ($playersData as $playerData) {
            $player = Server::getInstance()->getPlayer($playerData->getName());

            //テレポート
            $player->teleport($player->getSpawn());

            $player->addTitle("チームデスマッチ スタート");

            //Scoreboardのセット
            TDMScoreboard::send($player, $game->getMap()->getName(), 0, 0);
            //BossBarのセット
            $bossBar->send($player);

            //アイテムのセット

        }
    }
}