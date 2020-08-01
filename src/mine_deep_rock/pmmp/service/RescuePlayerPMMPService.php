<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\MilitaryDepartment;
use pocketmine\Player;
use team_game_system\TeamGameSystem;

class RescuePlayerPMMPService
{
    static function execute(Player $player, Player $target): void {
        $playerData = TeamGameSystem::getPlayerData($player);
        $targetData = TeamGameSystem::getPlayerData($target);

        if ($playerData->getTeamId() === null || $targetData->getTeamId() === null) {
            return;
        }

        if ($playerData->getTeamId()->equals($targetData->getTeamId())) {
            $playerStatus = PlayerStatusDAO::get($player->getName());
            if ($playerStatus->getMilitaryDepartment()->getName() === MilitaryDepartment::NursingSoldier) {
                ResortToTDMPMMPService::execute($target, $target->getPosition());
            }
        }
    }
}