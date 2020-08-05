<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use private_name_tag\models\PrivateNameTag;
use team_game_system\model\GameId;
use team_game_system\TeamGameSystem;

class ShowPrivateNameTagToParticipantsPMMPService
{
    static function execute(Player $target, GameId $gameId): void {
        $tag = PrivateNameTag::get($target);
        if ($tag === null) {
            SetPrivateNameTagPMMPService::execute($target);
            return;
        }

        $server = Server::getInstance();
        $participants = array_map(function ($participant) use ($server) {
            return $server->getPlayer($participant->getName());
        }, TeamGameSystem::getGamePlayersData($gameId));

        $tag->updateViewers($participants);
    }
}