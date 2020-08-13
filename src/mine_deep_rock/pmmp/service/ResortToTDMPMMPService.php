<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\model\PlayerGameStatus;
use mine_deep_rock\service\GivePlayerMoneyService;
use mine_deep_rock\service\UpdatePlayerGameStatusIsResuscitated;
use mine_deep_rock\store\PlayerGameStatusStore;
use pocketmine\level\Position;
use pocketmine\Player;
use team_game_system\model\Score;
use team_game_system\TeamGameSystem;

class ResortToTDMPMMPService
{
    static function execute(Player $player, Position $pos = null, bool $addScore = false): void {
        $playerData = TeamGameSystem::getPlayerData($player);
        if ($playerData->getTeamId() === null) {
            return;
        }

        //TODO:２チームしか想定していない
        if ($addScore) {
            $game = TeamGameSystem::getGame($playerData->getGameId());
            foreach ($game->getTeams() as $team) {
                if (!$team->getId()->equals($playerData->getTeamId())) {
                    TeamGameSystem::addScore($game->getId(), $team->getId(), new Score(1));
                }
            }
        }

        $player->setGamemode(Player::ADVENTURE);
        $player->setImmobile(false);

        if ($pos !== null) {
            //蘇生判定
            $player->teleport($pos);
        } else {
            TeamGameSystem::setSpawnPoint($player);
            $player->teleport($player->getSpawn());

            UpdatePlayerGameStatusIsResuscitated::execute($player->getName());
        }

        RemoveCadaverEntityPMMPService::execute($player);

        InitTDMEquipmentsPMMPService::execute($player);

        ShowPrivateNameTagToAllyPMMPService::execute($player, $playerData->getTeamId());
    }
}