<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\CorePvPMapDataDAO;
use mine_deep_rock\data_model\CandidateCorePositionsGroup;
use mine_deep_rock\data_model\CorePvPMapData;

class AddCandidateCorePositionsGroupService
{
    static function execute(string $mapName): void {
        $map = CorePvPMapDataDAO::getMapData($mapName);

        $groups = $map->getCandidateCorePositionsGroups();
        $groups[] = new CandidateCorePositionsGroup([]);

        CorePvPMapDataDAO::update($mapName, $groups);
    }
}