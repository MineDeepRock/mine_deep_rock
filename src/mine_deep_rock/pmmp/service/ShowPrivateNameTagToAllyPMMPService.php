<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\Player;
use pocketmine\Server;
use private_name_tag\PrivateNameTag;
use team_game_system\TeamGameSystem;

class ShowPrivateNameTagToAllyPMMPService
{
    static function execute(Player $target): void {
        $playerData = TeamGameSystem::getPlayerData($target);
        if ($playerData->getGameId() === null) return;


        $tag = PrivateNameTag::get($target);
        if ($tag === null) {
            SetPrivateNameTagPMMPService::execute($target);
            $tag = PrivateNameTag::get($target);
        }
        $server = Server::getInstance();

        $allyPlayers = [];
        foreach (TeamGameSystem::getTeamPlayersData($playerData->getTeamId()) as $allyPlayerData) {
            if ($allyPlayerData->getName() !== $target->getName()) {
                $allyPlayers[] = $server->getPlayer($allyPlayerData->getName());
            }
        }

        $tag->updateViewers($allyPlayers);
    }
}