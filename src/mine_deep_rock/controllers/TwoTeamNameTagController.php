<?php

namespace mine_deep_rock\controllers;


use pocketmine\Player;
use pocketmine\utils\TextFormat;
use team_name_tag_system\pmmp\entities\NameTagEntity;
use team_name_tag_system\TeamNameTagSystem;
use team_system\models\Game;
use team_system\TeamSystem;

abstract class TwoTeamNameTagController
{
    static function showToAlly(Player $target, Game $game): void {
        self::delete($target);

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

        if ($targetTeamId->equal($game->getRedTeamId())) {
            $name = TextFormat::RED . $target->getName();
        } else {
            $name = TextFormat::BLUE . $target->getName();
        }
        $hpGauge = str_repeat(TextFormat::GREEN . "■", intval($target->getHealth()));
        $hpGauge .= str_repeat(TextFormat::WHITE . "■", 20 - intval($target->getHealth()));
        TeamNameTagSystem::set($target, $name . "\n" . $hpGauge, $players);
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

        TeamNameTagSystem::updateNameTag($target, $name . "\n" . $hpGauge);
    }

    static function showToParticipants(Player $target, Game $game): void {
        self::delete($target);

        $targetTeamId = TeamSystem::getPlayerData($target->getName())->getBelongTeamId();
        if ($targetTeamId === null) return;

        $players = [];

        foreach ($target->getLevel()->getPlayers() as $player) {
            $playerData = TeamSystem::getPlayerData($player->getName());
            if ($playerData->getBelongTeamId() !== null) {
                $players[] = $player;
            }
        }

        if ($targetTeamId->equal($game->getRedTeamId())) {
            $name = TextFormat::RED . $target->getName();
        } else {
            $name = TextFormat::BLUE . $target->getName();
        }
        $hpGauge = str_repeat(TextFormat::GREEN . "■", intval($target->getHealth()));
        $hpGauge .= str_repeat(TextFormat::WHITE . "■", 20 - intval($target->getHealth()));
        TeamNameTagSystem::set($target, $name . "\n" . $hpGauge, $players);
    }

    static function delete(Player $player) {
        foreach ($player->getLevel()->getEntities() as $entity) {
            if ($entity instanceof NameTagEntity) {
                if ($entity->getOwnerName() === $player->getName()) {
                    $entity->kill();
                }
            }
        }
    }
}