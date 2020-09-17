<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\CorePvPMapDataDAO;

class DeleteCandidateCorePositionsGroup
{
    static function execute(string $mapName, int $groupIndex): void {
        $map = CorePvPMapDataDAO::getMapData($mapName);
        $groups = $map->getCandidateCorePositionsGroups();
        unset($groups[$groupIndex]);

        $groups = array_values($groups);
        CorePvPMapDataDAO::update($mapName, $groups);
    }
}