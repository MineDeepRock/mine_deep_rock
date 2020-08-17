<?php


namespace mine_deep_rock\dao;


use mine_deep_rock\adapter\DominationFlagDataJsonAdapter;
use mine_deep_rock\DataFolderPath;
use mine_deep_rock\model\DominationFlagData;
use team_game_system\adapter\MapJsonAdapter;

class DominationFlagDataDAO
{
    static function init() {
        if (!file_exists(DataFolderPath::DominationFlagData)) {
            mkdir(DataFolderPath::DominationFlagData);
        }
    }

    static function isExist(string $mapName): bool {
        return file_exists(DataFolderPath::DominationFlagData . $mapName . ".json");
    }

    static function initMap(string $mapName): void {
        file_put_contents(DataFolderPath::DominationFlagData . $mapName . ".json", json_encode([]));
    }

    /**
     * @return string[]
     */
    static function getRegisteredMapNames(): array {
        $names = [];
        $dh = opendir(DataFolderPath::DominationFlagData);
        while (($fileName = readdir($dh)) !== false) {
            if (filetype(DataFolderPath::DominationFlagData . $fileName) === "file") {
                $names[] = $fileName;
            }
        }

        closedir($dh);

        return $names;
    }

    /**
     * @param string $mapName
     * @return DominationFlagData[]
     */
    static function getFlagDataList(string $mapName): array {
        if (!file_exists(DataFolderPath::DominationFlagData . $mapName . ".json")) return [];

        $data = json_decode(file_get_contents(DataFolderPath::DominationFlagData . $mapName . ".json"), true);
        return array_map(function (array $json) {
            return DominationFlagDataJsonAdapter::decode($json);
        }, $data);
    }

    static function addFlagData(string $mapName, DominationFlagData $flagData): void {
        $flags = self::getFlagDataList($mapName);
        $flags[] = $flagData;

        $data = [];
        foreach ($flags as $flag) {
            $data[] = DominationFlagDataJsonAdapter::encode($flag);
        }

        file_put_contents(DataFolderPath::DominationFlagData . $mapName . ".json", json_encode($flags));
    }

    static function removeFlagData(string $mapName, DominationFlagData $flagData): void {
        $flags = self::getFlagDataList($mapName);
        foreach ($flags as $index => $flag) {
            if ($flag->getName() === $flagData->getName()) {
                unset($flags[$index]);
            }
        }

        $data = [];
        foreach ($flags as $flag) {
            $data[] = DominationFlagDataJsonAdapter::encode($flag);
        }

        file_put_contents(DataFolderPath::DominationFlagData . $mapName . ".json", json_encode($flags));
    }
}