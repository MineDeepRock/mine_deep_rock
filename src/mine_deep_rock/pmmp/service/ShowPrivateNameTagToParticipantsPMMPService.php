<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\Player;
use pocketmine\Server;
use private_name_tag\models\PrivateNameTag;
use team_game_system\model\GameId;
use team_game_system\TeamGameSystem;

class ShowPrivateNameTagToParticipantsPMMPService
{
    static function execute(Player $target, GameId $gameId): void {
        $tag = PrivateNameTag::get($target);
        if ($tag === null) SetPrivateNameTagPMMPService::execute($target);

        $tag = PrivateNameTag::get($target);
        $server = Server::getInstance();

        $participants = [];
        foreach (TeamGameSystem::getGamePlayersData($gameId) as $participant) {
            if ($participant->getName() !== $target->getName()) {
                $participants[] = $server->getPlayer($participant->getName());
            }
        }

        $tag->updateViewers($participants);
    }
}