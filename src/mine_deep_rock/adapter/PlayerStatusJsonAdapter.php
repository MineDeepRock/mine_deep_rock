<?php


namespace mine_deep_rock\adapter;


use mine_deep_rock\model\PlayerStatus;
use mine_deep_rock\model\Skill;
use mine_deep_rock\store\MilitaryDepartmentsStore;

class PlayerStatusJsonAdapter
{
    static function encode(PlayerStatus $playerStatus): array {
        return [
            "name" => $playerStatus->getName(),
            "military_department" => $playerStatus->getMilitaryDepartment()->getName(),
            "main_gun" => $playerStatus->getMainGunName(),
            "sub_gun" => $playerStatus->getSubGunName(),
            "owning_skills" => array_map(function (Skill $skill) {
                return $skill::Name;
            }, $playerStatus->getOwningSkills()),
            "selected_skills" => array_map(function (Skill $skill) {
                return $skill::Name;
            }, $playerStatus->getSelectedSkills()),
            "money" => $playerStatus->getMoney(),
        ];
    }

    static function decode(array $json): PlayerStatus {
        return new PlayerStatus(
            $json["name"],
            MilitaryDepartmentsStore::get($json["military_department"]),
            $json["main_gun"],
            $json["sub_gun"],
            $json["owning_skills"],
            $json["selected_skills"],
            $json["money"]
        );
    }
}