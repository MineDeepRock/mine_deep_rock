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
        $server = Server::getInstance();

        $allyPlayers = array_map(function ($allyPlayerData) use ($server) {
            return $server->getPlayer($allyPlayerData->getName());
        }, TeamGameSystem::getTeamPlayersData($teamId));

        $hpGauge = str_repeat(TextFormat::GREEN . "■", intval($target->getHealth()));
        $hpGauge .= str_repeat(TextFormat::WHITE . "■", 20 - intval($target->getHealth()));
        $tag = new PrivateNameTag($target, "{$target->getName()} \n {$hpGauge}", $allyPlayers);
        $tag->set();
    }
}