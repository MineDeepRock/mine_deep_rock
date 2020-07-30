<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use private_name_tag\models\PrivateNameTag;
use team_game_system\model\TeamId;
use team_game_system\TeamGameSystem;

class ShowPrivateNameTagToAllyPMMPService
{
    static function execute(Player $target, TeamId $teamId): void {
        $tag = PrivateNameTag::get($target);
        if ($tag === null) {
            //TODO : エラーを吐くように
            return;
        }

        $server = Server::getInstance();

        $allyPlayers = array_map(function ($allyPlayerData) use ($server) {
            return $server->getPlayer($allyPlayerData->getName());
        }, TeamGameSystem::getTeamPlayersData($teamId));

        $tag->updateViewers($allyPlayers);
    }
}