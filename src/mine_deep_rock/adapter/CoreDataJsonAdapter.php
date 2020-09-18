<?php


namespace mine_deep_rock\adapter;


use mine_deep_rock\data_model\CoreData;
use pocketmine\math\Vector3;

class CoreDataJsonAdapter
{
    static function encode(CoreData $coreData): array {
        return [
            "team_color" => $coreData->getTeamColor(),
            "x" => $coreData->getCoordinate()->getX(),
            "y" => $coreData->getCoordinate()->getY(),
            "z" => $coreData->getCoordinate()->getZ()
        ];
    }

    static function decode(array $json): CoreData {
        $position = new Vector3($json["x"], $json["y"], $json["z"]);
        return new CoreData($json["team_color"], $position);
    }
}