<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\Player;
use team_game_system\TeamGameSystem;

class SpawnOnTDMPMMPService
{
    static function execute(Player $player): void {
        $player->setGamemode(Player::ADVENTURE);
        $player->setImmobile(false);

        RemoveCadaverEntityPMMPService::execute($player);

        InitTDMEquipmentsPMMPService::execute($player);

        $playerData = TeamGameSystem::getPlayerData($player);
        ShowPrivateNameTagToAllyPMMPService::execute($player, $playerData->getTeamId());
    }
}