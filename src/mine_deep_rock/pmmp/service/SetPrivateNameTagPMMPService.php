<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\Player;
use pocketmine\utils\TextFormat;
use private_name_tag\PrivateNameTag;
use team_game_system\TeamGameSystem;

class SetPrivateNameTagPMMPService
{
    static function execute(Player $player): void {
        $tag = PrivateNameTag::get($player);
        if ($tag !== null) {
            var_dump("すでにプライベートネームタグがセットされています");
            //throw new LogicException("すでにプライベートネームタグがセットされています");
        }

        $health = $player->getHealth();

        if ($health <= 0) {
            $hpGauge = str_repeat(TextFormat::WHITE . "■", 20);
        } else if ($health >= 20) {
            $hpGauge = str_repeat(TextFormat::GREEN . "■", 20);
        } else {
            $hpGauge = str_repeat(TextFormat::GREEN . "■", $health);
            $hpGauge .= str_repeat(TextFormat::WHITE . "■", 20 - $health);
        }

        $playerData = TeamGameSystem::getPlayerData($player);
        if ($playerData->getGameId() === null) return;
        $playerTeam = TeamGameSystem::getTeam($playerData->getGameId(), $playerData->getTeamId());

        $tag = new PrivateNameTag($player, $playerTeam->getTeamColorFormat() . "{$player->getName()} \n {$hpGauge}", []);
        $tag->set();
    }
}