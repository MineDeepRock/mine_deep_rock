<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\CorePvPMapDataDAO;
use mine_deep_rock\data_model\CandidateCorePositionsGroup;
use pocketmine\level\Position;

class DeleteCandidateCorePositionService
{
    static function execute(string $mapName, int $groupIndex, Position $targetPosition): void {
        $map = CorePvPMapDataDAO::getMapData($mapName);
        $groups = $map->getCandidateCorePositionsGroups();

        $positions = $groups[$groupIndex]->getPositions();

        foreach ($positions as $index => $position) {
            if ($position->equals($targetPosition)) {
                unset($positions[$index]);
            }
        }
        $positions = array_values($positions);
        $newGroup = new CandidateCorePositionsGroup($positions);

        $groups[$groupIndex] = $newGroup;
        CorePvPMapDataDAO::update($mapName, $groups);
    }
}