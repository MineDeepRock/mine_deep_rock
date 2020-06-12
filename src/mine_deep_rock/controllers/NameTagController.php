<?php

namespace mine_deep_rock\controllers;


use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use team_name_tag_system\pmmp\entities\NameTagEntity;
use team_name_tag_system\TeamNameTagSystem;
use team_system\models\GameId;
use team_system\models\PlayerData;
use team_system\models\TeamId;
use team_system\TeamSystem;

class NameTagController
{
    public static function showToAlly(Player $player, GameId $gameId, TeamId $redTeamId, Server $server): void {
        self::delete($player);
        $players = [];

        $playerTeamId = TeamSystem::getPlayerData($player->getName())->getBelongTeamId();
        foreach (TeamSystem::getParticipantData($gameId) as $participant) {
            if ($participant instanceof PlayerData) {
                if ($participant->getBelongTeamId()->equal($playerTeamId)) {
                    $players[] = $server->getPlayer($participant->getName());
                }
            }
        }

        if ($playerTeamId->equal($redTeamId)) {
            $name = TextFormat::RED . $player->getName();
        } else {
            $name = TextFormat::BLUE . $player->getName();
        }
        $hpGauge = str_repeat(TextFormat::GREEN . "■", intval($player->getHealth()));
        $hpGauge .= str_repeat(TextFormat::WHITE . "■", 20 - intval($player->getHealth()));
        TeamNameTagSystem::set($player, $name . "\n" . $hpGauge, $players);
    }

    public static function update(Player $player, TeamId $redTeamId): void {
        $hpGauge = str_repeat(TextFormat::GREEN . "■", intval($player->getHealth()));
        $hpGauge .= str_repeat(TextFormat::WHITE . "■", 20 - intval($player->getHealth()));

        $playerTeamId = TeamSystem::getPlayerData($player->getName())->getBelongTeamId();
        if ($playerTeamId->equal($redTeamId)) {
            $name = TextFormat::RED . $player->getName();
        } else {
            $name = TextFormat::BLUE . $player->getName();
        }

        TeamNameTagSystem::updateNameTag($player, $name . "\n" . $hpGauge);
    }

    public static function showToParticipant(Player $player, GameId $gameId, TeamId $redTeamId, Server $server): void {
        self::delete($player);
        $players = [];

        $playerTeamId = TeamSystem::getPlayerData($player->getName())->getBelongTeamId();
        foreach (TeamSystem::getParticipantData($gameId) as $participant) {
            if ($participant instanceof PlayerData) {
                $players[] = $server->getPlayer($participant->getName());
            }
        }

        if ($playerTeamId->equal($redTeamId)) {
            $name = TextFormat::RED . $player->getName();
        } else {
            $name = TextFormat::BLUE . $player->getName();
        }
        $hpGauge = str_repeat(TextFormat::GREEN . "■", intval($player->getHealth()));
        $hpGauge .= str_repeat(TextFormat::WHITE . "■", 20 - intval($player->getHealth()));
        TeamNameTagSystem::set($player, $name . "\n" . $hpGauge, $players);
    }

    public static function delete(Player $player) {
        foreach ($player->getLevel()->getEntities() as $entity) {
            if ($entity instanceof NameTagEntity) {
                if ($entity->getOwnerName() === $player->getName()) {
                    $entity->kill();
                }
            }
        }
    }
}