<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\CorePvPMapDataDAO;
use mine_deep_rock\data_model\CoreData;

class AddCoreDataService
{
    static function execute(string $mapName, CoreData $coreData): void {
        $map = CorePvPMapDataDAO::getMapData($mapName);
        $coreDataList = $map->getCoreDataList();
        $coreDataList[] = $coreData;

        CorePvPMapDataDAO::update($mapName, $coreDataList);
    }
}