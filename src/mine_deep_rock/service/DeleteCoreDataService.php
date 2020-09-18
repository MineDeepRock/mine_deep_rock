<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\CorePvPMapDataDAO;
use mine_deep_rock\data_model\CoreData;

class DeleteCoreDataService
{
    static function execute(string $mapName, CoreData $targetCoreData): void {
        $map = CorePvPMapDataDAO::getMapData($mapName);
        $coreDataList = $map->getCoreDataList();
        foreach ($coreDataList as $index => $coreData) {
            if ($coreData->getTeamColor() === $targetCoreData->getTeamColor()) {
                unset($coreDataList[$index]);
            }
        }

        $coreDataList = array_values($coreDataList);
        CorePvPMapDataDAO::update($mapName, $coreDataList);
    }
}