<?php


namespace mine_deep_rock\adapter;


use mine_deep_rock\data_model\CorePvPMapData;
use pocketmine\Server;
use team_game_system\store\MapsStore;

class CorePvPMapDataJsonAdapter
{
    static function encode(CorePvPMapData $corePvPMapData): array {
        return [
            "map_name" => $corePvPMapData->getMapName(),
            "candidate_core_positions_groups" => array_map(function ($group) {
                return CandidateCorePositionsGroupJsonAdapter::encode($group);
            }, $corePvPMapData->getCandidateCorePositionsGroups()),
        ];
    }

    static function decode(array $json): CorePvPMapData {
        $levelName = MapsStore::findByName($json["map_name"])->getLevelName();
        $level = Server::getInstance()->getLevelByName($levelName);

        return new CorePvPMapData(
            $json["map_name"],
            array_map(function ($group) use ($level) {
                CandidateCorePositionsGroupJsonAdapter::decode($level, $group);
            }, $json["candidate_core_positions_groups"])
        );
    }
}