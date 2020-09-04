<?php


namespace mine_deep_rock\adapter;


use mine_deep_rock\model\PlayerEquipments;
use mine_deep_rock\model\Skill;
use mine_deep_rock\store\MilitaryDepartmentsStore;

class PlayerEquipmentsJsonAdapter
{
    static function encode(PlayerEquipments $playerEquipments): array {
        return [
            "name" => $playerEquipments->getName(),
            "military_department" => $playerEquipments->getMilitaryDepartment()->getName(),
            "main_gun" => $playerEquipments->getMainGunName(),
            "sub_gun" => $playerEquipments->getSubGunName(),
            "selected_skills" => array_map(function (Skill $skill) {
                return $skill::Name;
            }, $playerEquipments->getSelectedSkills()),
        ];
    }

    static function decode(array $json): PlayerEquipments {

        $selectedSkills = [];
        if (key_exists("selected_skills", $json)) {
            $selectedSkills = array_map(function ($name) {
                return Skill::fromString($name);
            }, $json["selected_skills"]);
        }

        return new PlayerEquipments(
            $json["name"],
            MilitaryDepartmentsStore::get($json["military_department"]),
            $json["main_gun"],
            $json["sub_gun"],
            $selectedSkills
        );
    }
}