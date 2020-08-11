<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\Player;
use pocketmine\Server;
use private_name_tag\models\PrivateNameTag;
use team_game_system\model\TeamId;
use team_game_system\TeamGameSystem;

class ShowPrivateNameTagToAllyPMMPService
{
    static function execute(Player $target, TeamId $teamId): void {
        $tag = PrivateNameTag::get($target);
        if ($tag === null) SetPrivateNameTagPMMPService::execute($target);

        $tag = PrivateNameTag::get($target);
        $server = Server::getInstance();

        $allyPlayers = [];
        foreach (TeamGameSystem::getTeamPlayersData($teamId) as $allyPlayerData) {
            if ($allyPlayerData->getName() !== $target->getName()) {
                $allyPlayers[] = $server->getPlayer($allyPlayerData->getName());
            }
        }

        $tag->updateViewers($allyPlayers);
    }
}