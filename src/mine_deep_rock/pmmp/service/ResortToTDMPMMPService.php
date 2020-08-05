<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\level\Position;
use pocketmine\Player;
use team_game_system\TeamGameSystem;

class ResortToTDMPMMPService
{
    static function execute(Player $player, Position $pos = null): void {
        $playerData = TeamGameSystem::getPlayerData($player);
        if ($playerData->getTeamId() === null) {
            return;
        }

        $player->setGamemode(Player::ADVENTURE);
        $player->setImmobile(false);

        if ($pos !== null) {
            $player->teleport($pos);
        } else {
            TeamGameSystem::setSpawnPoint($player);
            $player->teleport($player->getSpawn());
        }

        RemoveCadaverEntityPMMPService::execute($player);

        InitTDMEquipmentsPMMPService::execute($player);

        ShowPrivateNameTagToAllyPMMPService::execute($player, $playerData->getTeamId());
    }
}