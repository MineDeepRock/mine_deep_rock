<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\CorePvPMapDataDAO;
use mine_deep_rock\data_model\CandidateCorePositionsGroup;
use pocketmine\level\Position;

class AddCandidateCorePositionService
{
    static function execute(string $mapName, int $groupIndex, Position $position): void {
        $map = CorePvPMapDataDAO::getMapData($mapName);
        $groups = $map->getCandidateCorePositionsGroups();

        $newPositions = $groups[$groupIndex]->getPositions();
        $newPositions[] = $position;
        $newGroup = new CandidateCorePositionsGroup($newPositions);

        $groups[$groupIndex] = $newGroup;
        CorePvPMapDataDAO::update($mapName, $groups);
    }
}