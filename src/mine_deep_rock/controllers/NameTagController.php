<?php

namespace mine_deep_rock\controllers;


use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use team_name_tag_system\TeamNameTagSystem;
use team_system\models\GameId;
use team_system\models\PlayerData;
use team_system\TeamSystem;

class NameTagController
{

    static function set(Player $player, GameId $gameId, Server $server): void {
        $players = [];

        $attackerTeamId = TeamSystem::getPlayerData($player->getName())->getBelongTeamId();
        foreach (TeamSystem::getParticipantData($gameId) as $participant) {
            if ($participant instanceof PlayerData) {
                if ($participant->getBelongTeamId()->equal($attackerTeamId)) {
                    $players[] = $server->getPlayer($participant->getName());
                }
            }
        }

        $hpGauge = str_repeat(TextFormat::RED . "■", intval($player->getHealth()));
        $hpGauge .= str_repeat(TextFormat::WHITE . "■", 20 - intval($player->getHealth()));
        TeamNameTagSystem::set($player, $player->getName() . "\n" . $hpGauge, $players);
    }

    static function update(Player $player): void {
        $hpGauge = str_repeat(TextFormat::RED . "■", intval($player->getHealth()));
        $hpGauge .= str_repeat(TextFormat::WHITE . "■", 20 - intval($player->getHealth()));
        TeamNameTagSystem::updateNameTag($player, $player->getName() . "\n" . $hpGauge);
    }
}