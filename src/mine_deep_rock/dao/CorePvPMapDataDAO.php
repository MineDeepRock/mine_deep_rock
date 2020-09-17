<?php


namespace mine_deep_rock\dao;


use mine_deep_rock\adapter\CorePvPMapDataJsonAdapter;
use mine_deep_rock\data_model\CorePvPMapData;
use mine_deep_rock\DataFolderPath;

class CorePvPMapDataDAO
{
    static function init() {
        if (!file_exists(DataFolderPath::CorePvPMapData)) {
            mkdir(DataFolderPath::CorePvPMapData);
        }
    }

    static function isExist(string $mapName): bool {
        return file_exists(DataFolderPath::CorePvPMapData . $mapName . ".json");
    }

    static function registerMap(string $mapName): void {
        $array = CorePvPMapDataJsonAdapter::encode(new CorePvPMapData($mapName, []));
        file_put_contents(DataFolderPath::CorePvPMapData . $mapName . ".json", json_encode($array));
    }

    static function getMapData(string $mapName): CorePvPMapData {
        $data = json_decode(file_get_contents(DataFolderPath::CorePvPMapData . $mapName . ".json"), true);
        return CorePvPMapDataJsonAdapter::decode($data);
    }

    /**
     * @return string[]
     */
    static function getRegisteredMapNames(): array {
        $names = [];
        $dh = opendir(DataFolderPath::CorePvPMapData);
        while (($fileName = readdir($dh)) !== false) {
            if (filetype(DataFolderPath::CorePvPMapData . $fileName) === "file") {
                $names[] = str_replace(".json", "", $fileName);
            }
        }

        closedir($dh);

        return $names;
    }

    static function update(string $mapName, array $candidateCorePositionsGroups): void {
        $array = CorePvPMapDataJsonAdapter::encode(new CorePvPMapData($mapName, $candidateCorePositionsGroups));
        file_put_contents(DataFolderPath::CorePvPMapData . $mapName . ".json", json_encode($array));
    }
}