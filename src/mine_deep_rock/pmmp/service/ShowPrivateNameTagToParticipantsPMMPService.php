<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use private_name_tag\models\PrivateNameTag;
use team_game_system\data_model\PlayerData;
use team_game_system\model\GameId;
use team_game_system\TeamGameSystem;

class ShowPrivateNameTagToParticipantsPMMPService
{
    static function execute(Player $target, GameId $gameId): void {
        /** @var PlayerData[] $playersData */

        $server = Server::getInstance();
        $participants = array_map(function ($participant) use ($server) {
            return $server->getPlayer($participant->getName());
        }, TeamGameSystem::getGamePlayersData($gameId));


        $hpGauge = str_repeat(TextFormat::GREEN . "■", intval($target->getHealth()));
        $hpGauge .= str_repeat(TextFormat::WHITE . "■", 20 - intval($target->getHealth()));
        $tag = new PrivateNameTag($target, "{$target->getName()} \n {$hpGauge}", $participants);
        $tag->set();
    }
}