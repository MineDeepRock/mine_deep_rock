<?php


namespace mine_deep_rock\adapter;


use mine_deep_rock\data_model\CorePvPMapData;
use pocketmine\Server;
use team_game_system\store\MapsStore;

class CorePvPMapDataJsonAdapter
{
    static function encode(CorePvPMapData $corePvPMapData): array {


        $coreDataList = [];
        foreach ($corePvPMapData->getCoreDataList() as $coreData) {
            $coreDataList[] = CoreDataJsonAdapter::encode($coreData);
        }

        return [
            "map_name" => $corePvPMapData->getMapName(),
            "core_data_list" => $coreDataList,
        ];
    }

    static function decode(array $json): CorePvPMapData {
        $coreDataList = [];
        foreach ($json["core_data_list"] as $coreData) {
            $coreDataList[] = CoreDataJsonAdapter::decode($coreData);
        }

        return new CorePvPMapData(
            $json["map_name"],
            $coreDataList
        );
    }
}