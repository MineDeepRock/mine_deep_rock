<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\MilitaryDepartment;
use mine_deep_rock\service\SelectMilitaryDepartmentService;
use mine_deep_rock\service\UpdatePlayerGameStatusIsResuscitated;
use pocketmine\level\Position;
use pocketmine\Player;
use team_game_system\model\Score;
use team_game_system\TeamGameSystem;

class ResortPMMPService
{
    static function execute(Player $player, Position $pos = null, bool $byRescue = false): void {
        $playerData = TeamGameSystem::getPlayerData($player);
        if ($playerData->getTeamId() === null) {
            return;
        }

        if ($byRescue) {
            //TODO:２チームしか想定していない
            $game = TeamGameSystem::getGame($playerData->getGameId());
            foreach ($game->getTeams() as $team) {
                if (!$team->getId()->equals($playerData->getTeamId())) {
                    TeamGameSystem::addScore($game->getId(), $team->getId(), new Score(1));
                }
            }
        } else {
            $status = PlayerStatusDAO::get($playerData->getName());
            if ($status->getMilitaryDepartment()->getName() === MilitaryDepartment::Sentry) {
                SelectMilitaryDepartmentService::execute($player->getName(), MilitaryDepartment::AssaultSoldier);
            }

        }

        $player->setGamemode(Player::ADVENTURE);
        $player->setImmobile(false);

        $game = TeamGameSystem::getGame($playerData->getGameId());
        if ($game === null) return;

        if ($pos !== null) {
            //蘇生判定
            $player->teleport($pos);
        } else {
            TeamGameSystem::setSpawnPoint($player);
            $player->teleport($player->getSpawn());

            UpdatePlayerGameStatusIsResuscitated::execute($player->getName());
        }

        RemoveCadaverEntityPMMPService::execute($player);

        InitEquipmentsPMMPService::execute($player);

        InitEffectsPMMPService::execute($player);

        ShowPrivateNameTagToAllyPMMPService::execute($player, $playerData->getTeamId());
    }
}