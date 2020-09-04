<?php


namespace mine_deep_rock\dao;


use mine_deep_rock\adapter\PlayerEquipmentsJsonAdapter;
use mine_deep_rock\DataFolderPath;
use mine_deep_rock\model\PlayerEquipments;

class PlayerEquipmentsDAO
{
    private static $path = DataFolderPath::PlayerEquipments;

    static function init() {
        if (!file_exists(self::$path)) {
            mkdir(self::$path);
        }
    }

    static function isExist(string $name):bool {
        return file_exists(self::$path . $name . ".json");
    }

    static function save(PlayerEquipments $playerEquipments): void {
        $data = json_encode(PlayerEquipmentsJsonAdapter::encode($playerEquipments));
        file_put_contents(self::$path . $playerEquipments->getName() . ".json", $data);
    }

    static function get(string $name): ?PlayerEquipments {
        if (!file_exists(self::$path . $name . ".json")) return null;

        $data = json_decode(file_get_contents(self::$path . $name . ".json"), true);
        return PlayerEquipmentsJsonAdapter::decode($data);
    }

    static function update(PlayerEquipments $playerEquipments): void {
        file_put_contents(self::$path . $playerEquipments->getName() . ".json", json_encode(PlayerEquipmentsJsonAdapter::encode($playerEquipments)));
    }
}