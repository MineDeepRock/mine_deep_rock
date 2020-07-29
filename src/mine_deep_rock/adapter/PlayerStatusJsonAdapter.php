<?php


namespace mine_deep_rock\adapter;


use mine_deep_rock\model\PlayerStatus;
use mine_deep_rock\store\MilitaryDepartmentsStore;

class PlayerStatusJsonAdapter
{
    static function encode(PlayerStatus $playerStatus): array {
        return [
            "name" => $playerStatus->getName(),
            "military_department" => $playerStatus->getMilitaryDepartment()->getName()
        ];
    }

    static function decode(array $json): PlayerStatus {
        return new PlayerStatus($json["name"], MilitaryDepartmentsStore::get($json["military_department"]));
    }
}