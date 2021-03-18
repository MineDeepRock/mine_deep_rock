<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\Player;
use pocketmine\utils\TextFormat;
use private_name_tag\PrivateNameTag;
use team_game_system\TeamGameSystem;

class UpdatePrivateNameTagPMMPService
{
    static function execute(Player $target, ?int $health = null): void {
        $tag = PrivateNameTag::get($target);
        if ($tag === null) return;


        $health = $health ?? $target->getHealth();

        if ($health <= 0) {
            $hpGauge = str_repeat(TextFormat::WHITE . "■", 20);
        } else if ($health >= 20) {
            $hpGauge = str_repeat(TextFormat::GREEN . "■", 20);
        } else {
            $hpGauge = str_repeat(TextFormat::GREEN . "■", $health);
            $hpGauge .= str_repeat(TextFormat::WHITE . "■", 20 - $health);
        }

        $playerData = TeamGameSystem::getPlayerData($target);
        if ($playerData->getGameId() === null) return;
        $playerTeam = TeamGameSystem::getTeam($playerData->getGameId(), $playerData->getTeamId());

        $tag->updateNameTag($playerTeam->getTeamColorFormat() . "{$target->getName()} \n {$hpGauge}");
    }
}