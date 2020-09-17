<?php


namespace mine_deep_rock\adapter;


use mine_deep_rock\data_model\CorePvPMapData;
use pocketmine\Server;
use team_game_system\store\MapsStore;

class CorePvPMapDataJsonAdapter
{
    static function encode(CorePvPMapData $corePvPMapData): array {


        $groups = [];
        foreach ($corePvPMapData->getCandidateCorePositionsGroups() as $group) {
            $groups[] = CandidateCorePositionsGroupJsonAdapter::encode($group);
        }

        return [
            "map_name" => $corePvPMapData->getMapName(),
            "candidate_core_positions_groups" => $groups,
        ];
    }

    static function decode(array $json): CorePvPMapData {
        $levelName = MapsStore::findByName($json["map_name"])->getLevelName();
        $level = Server::getInstance()->getLevelByName($levelName);

        $groups = [];
        foreach ($json["candidate_core_positions_groups"] as $group) {
            $groups[] = CandidateCorePositionsGroupJsonAdapter::decode($level, $group);
        }

        return new CorePvPMapData(
            $json["map_name"],
            $groups
        );
    }
}