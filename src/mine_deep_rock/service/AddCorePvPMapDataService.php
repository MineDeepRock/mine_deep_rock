<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\CorePvPMapDataDAO;

class AddCorePvPMapDataService
{
    static function execute(string $mapName): void {
        if (CorePvPMapDataDAO::isExist($mapName)) return;
        CorePvPMapDataDAO::registerMap($mapName);
    }
}