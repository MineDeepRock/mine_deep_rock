<?php


namespace mine_deep_rock\adapter;


use mine_deep_rock\model\PlayerStatus;
use mine_deep_rock\model\Skill;

class PlayerStatusJsonAdapter
{
    static function encode(PlayerStatus $playerStatus): array {
        return [
            "name" => $playerStatus->getName(),
            "owning_skills" => array_map(function (Skill $skill) {
                return $skill::Name;
            }, $playerStatus->getOwningSkills()),
            "money" => $playerStatus->getMoney(),
        ];
    }

    static function decode(array $json): PlayerStatus {

        $owingSkills = [];
        if (key_exists("owning_skills", $json)) {
            $owingSkills = array_map(function ($name) {
                return Skill::fromString($name);
            }, $json["owning_skills"]);
        }

        return new PlayerStatus(
            $json["name"],
            $owingSkills,
            $json["money"]
        );
    }
}