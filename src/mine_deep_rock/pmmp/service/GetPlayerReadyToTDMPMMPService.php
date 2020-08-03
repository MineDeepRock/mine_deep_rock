<?php


namespace mine_deep_rock\pmmp\service;


use bossbar_system\models\BossBar;
use mine_deep_rock\pmmp\scoreboard\PlayerStatusScoreboard;
use mine_deep_rock\pmmp\scoreboard\TDMScoreboard;
use pocketmine\Player;
use pocketmine\Server;
use team_game_system\data_model\PlayerData;
use team_game_system\model\GameId;
use team_game_system\TeamGameSystem;

class GetPlayerReadyToTDMPMMPService
{
    static function execute(PlayerData $playerData, GameId $gameId): void {
        $game = TeamGameSystem::getGame($gameId);
        $player = Server::getInstance()->getPlayer($playerData->getName());
        $playerTeam = null;
        foreach ($game->getTeams() as $team) {
            if ($playerData->getTeamId()->equals($team->getId())) $playerTeam = $team;
        }

        //テレポート
        TeamGameSystem::setSpawnPoint($player);
        $player->teleport($player->getSpawn());

        $player->addTitle("チームデスマッチ スタート");
        $player->sendMessage("あなたは{$playerTeam->getName()}チームです");

        //Scoreboardのセット
        PlayerStatusScoreboard::delete($player);
        TDMScoreboard::send($player, $game->getMap()->getName(), 0, 0);

        //BossBarのセット
        if ($game->getTimeLimit() === null) {
            $bossBar = new BossBar("経過時間:" . $game->getElapsedTime(), 1);
        } else {
            $bossBar = new BossBar("残り時間:" . ($game->getTimeLimit() - $game->getElapsedTime()), $game->getElapsedTime() / $game->getTimeLimit());
        }
        $bossBar->send($player);


        //アイテムのセット
        InitTDMEquipmentsPMMPService::execute($player);

        ShowPrivateNameTagToAllyPMMPService::execute($player, $playerData->getTeamId());
    }
}