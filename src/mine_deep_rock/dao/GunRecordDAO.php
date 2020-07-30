<?php


namespace mine_deep_rock\dao;


use mine_deep_rock\adapter\GunRecordJsonAdapter;
use mine_deep_rock\DataFolderPath;
use mine_deep_rock\model\GunRecord;

class GunRecordDAO
{
    static function init() {
        if (!file_exists(DataFolderPath::GunRecord)) {
            mkdir(DataFolderPath::GunRecord);
        }
    }
    static function isExist(string $ownerName): bool {
        return file_exists(DataFolderPath::GunRecord . $ownerName . ".json");
    }

    static function registerOwner(string $ownerName): void {
        file_put_contents(DataFolderPath::GunRecord . $ownerName . ".json", json_encode([]));
    }

    static function add(string $ownerName, GunRecord $gunRecord): void {
        $data = json_decode(file_get_contents(DataFolderPath::GunRecord . $ownerName . ".json"), true);
        if (is_array($data)) {
            $data[$gunRecord->getName()] = GunRecordJsonAdapter::encode($gunRecord);
            file_put_contents(DataFolderPath::GunRecord . $ownerName . ".json", json_encode($data));
        }
    }

    static function get(string $ownerName, string $weaponName): GunRecord {
        $data = json_decode(file_get_contents(DataFolderPath::GunRecord . $ownerName . ".json"), true);
        return GunRecordJsonAdapter::decode($data[$weaponName]);
    }

    /**
     * @param string $ownerName
     * @return GunRecord[]
     */
    static function getOwn(string $ownerName): array {
        $gunRecordJsonData = json_decode(file_get_contents(DataFolderPath::GunRecord . $ownerName . ".json"), true);
        if (is_array($gunRecordJsonData)) {
            return array_map(function ($weaponJsonData) {
                return GunRecordJsonAdapter::decode($weaponJsonData);
            }, $gunRecordJsonData);
        }
        return [];
    }

    static function update(string $ownerName, GunRecord $gunRecord): void {
        $data = json_decode(file_get_contents(DataFolderPath::GunRecord . $ownerName . ".json"), true);
        if (is_array($data)) {
            $data[$gunRecord->getName()] = GunRecordJsonAdapter::encode($gunRecord);
            file_put_contents(DataFolderPath::GunRecord . $ownerName . ".json", json_encode($data));
        }
    }
}