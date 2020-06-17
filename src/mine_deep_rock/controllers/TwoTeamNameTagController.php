<?php

namespace mine_deep_rock\controllers;


use pocketmine\Player;
use pocketmine\utils\TextFormat;
use private_name_tag\models\PrivateNameTag;
use team_system\models\Game;
use team_system\TeamSystem;

abstract class TwoTeamNameTagController
{
    static function set(Player $target, Game $game): void {
        $nameTag = new PrivateNameTag($target, $target->getName(), []);
        $nameTag->set();
        self::update($target, $game);
    }

    static function showToAlly(Player $target, Game $game): void {
        $targetTeamId = TeamSystem::getPlayerData($target->getName())->getBelongTeamId();
        if ($targetTeamId === null) return;

        $players = [];

        $playerTeamId = TeamSystem::getPlayerData($target->getName())->getBelongTeamId();
        foreach ($target->getLevel()->getPlayers() as $player) {
            $playerData = TeamSystem::getPlayerData($player->getName());
            if ($playerData->getBelongTeamId() !== null) {
                if ($playerData->getBelongTeamId()->equal($playerTeamId)) {
                    $players[] = $player;
                }
            }
        }

        $nameTag = PrivateNameTag::get($target);
        if ($nameTag !== null) $nameTag->updateViewers($players);
    }

    static function update(Player $target, Game $game): void {
        $hpGauge = str_repeat(TextFormat::GREEN . "■", intval($target->getHealth()));
        $hpGauge .= str_repeat(TextFormat::WHITE . "■", 20 - intval($target->getHealth()));

        $playerTeamId = TeamSystem::getPlayerData($target->getName())->getBelongTeamId();
        if ($playerTeamId === null) return;

        if ($playerTeamId->equal($game->getRedTeamId())) {
            $name = TextFormat::RED . $target->getName();
        } else {
            $name = TextFormat::BLUE . $target->getName();
        }

        $nameTag = PrivateNameTag::get($target);
        if ($nameTag !== null) $nameTag->updateNameTag("{$name}\n{$hpGauge}");
    }

    static function showToParticipants(Player $target, Game $game): void {
        $targetTeamId = TeamSystem::getPlayerData($target->getName())->getBelongTeamId();
        if ($targetTeamId === null) return;

        $players = [];

        foreach ($target->getLevel()->getPlayers() as $player) {
            $playerData = TeamSystem::getPlayerData($player->getName());
            if ($playerData->getBelongTeamId() !== null) {
                $players[] = $player;
            }
        }

        $nameTag = PrivateNameTag::get($target);
        if ($nameTag !== null) $nameTag->updateViewers($players);
    }
}