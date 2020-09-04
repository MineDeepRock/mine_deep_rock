<?php


namespace mine_deep_rock\adapter;


use mine_deep_rock\model\PlayerLevel;
use mine_deep_rock\model\PlayerStatus;
use mine_deep_rock\model\Skill;
use pocketmine\Player;

class PlayerStatusJsonAdapter
{
    static function encode(PlayerStatus $playerStatus): array {
        return [
            "name" => $playerStatus->getName(),
            "player_level" => [
                "rank" => $playerStatus->getLevel()->getRank(),
                "total_xp" => $playerStatus->getLevel()->getTotalXp(),
            ],
            "money" => $playerStatus->getMoney(),
            "owning_skills" => array_map(function (Skill $skill) {
                return $skill::Name;
            }, $playerStatus->getOwningSkills()),
        ];
    }

    static function decode(array $json): PlayerStatus {

        $owingSkills = [];
        if (key_exists("owning_skills", $json)) {
            $owingSkills = array_map(function ($name) {
                return Skill::fromString($name);
            }, $json["owning_skills"]);
        }

        $playerLevel = new PlayerLevel(
            intval($json["player_level"]["level"]),
            intval($json["player_level"]["total_xp"])
        );

        return new PlayerStatus(
            $json["name"],
            $playerLevel,
            $json["money"],
            $owingSkills
        );
    }
}