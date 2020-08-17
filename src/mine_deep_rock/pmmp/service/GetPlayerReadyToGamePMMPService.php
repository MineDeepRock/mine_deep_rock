<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\GameTypeList;
use mine_deep_rock\pmmp\scoreboard\DominationScoreboard;
use mine_deep_rock\pmmp\scoreboard\PlayerStatusScoreboard;
use mine_deep_rock\pmmp\scoreboard\TDMScoreboard;
use mine_deep_rock\store\DominationFlagsStore;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use team_game_system\data_model\PlayerData;
use team_game_system\model\GameId;
use team_game_system\TeamGameSystem;

class GetPlayerReadyToGamePMMPService
{
    static function execute(PlayerData $playerData, GameId $gameId): void {
        $game = TeamGameSystem::getGame($gameId);
        $player = Server::getInstance()->getPlayer($playerData->getName());
        $playerTeam = TeamGameSystem::getTeam($gameId, $playerData->getTeamId());

        //テレポート
        TeamGameSystem::setSpawnPoint($player);
        $player->teleport($player->getSpawn());

        $player->sendTitle("チームデスマッチ スタート");
        $player->sendMessage("あなたは" . $playerTeam->getTeamColorFormat() . $playerTeam->getName() . TextFormat::RESET . "チームです");

        //Scoreboardのセット
        PlayerStatusScoreboard::delete($player);
        $gameType = $game->getType();
        if ($gameType->equals(GameTypeList::TDM())) {
            TDMScoreboard::send($player, $game->getMap()->getName(), 0, 0);

        } else if ($gameType->equals(GameTypeList::Domination())) {
            DominationScoreboard::send($player, $game, 0, 0, DominationFlagsStore::findByGameId($gameId));
        }

        //アイテムのセット
        InitEquipmentsPMMPService::execute($player);

        //エフェクトをセット
        InitEffectsPMMPService::execute($player);
    }
}