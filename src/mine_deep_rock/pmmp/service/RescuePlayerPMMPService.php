<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\MilitaryDepartment;
use mine_deep_rock\model\PlayerGameStatus;
use mine_deep_rock\service\UpdatePlayerGameStatusIsResuscitated;
use mine_deep_rock\store\PlayerGameStatusStore;
use pocketmine\Player;
use team_game_system\TeamGameSystem;

class RescuePlayerPMMPService
{
    static function execute(Player $player, Player $target): void {
        $playerData = TeamGameSystem::getPlayerData($player);
        $targetData = TeamGameSystem::getPlayerData($target);
        if ($playerData->getTeamId() === null || $targetData->getTeamId() === null) return;

        $playerGameStatus = PlayerGameStatusStore::findByName($target->getName());
        if ($playerGameStatus->isResuscitated()) return;

        if ($playerData->getTeamId()->equals($targetData->getTeamId())) {
            $playerStatus = PlayerStatusDAO::get($player->getName());
            if ($playerStatus->getMilitaryDepartment()->getName() === MilitaryDepartment::NursingSoldier) {
                UpdatePlayerGameStatusIsResuscitated::execute($target->getName());
                ResortToTDMPMMPService::execute($target, $target->getPosition());

                $player->sendMessage($target->getName() . "を蘇生した");
                $target->sendMessage($player->getName() . "に蘇生されました");
            }
        }
    }
}