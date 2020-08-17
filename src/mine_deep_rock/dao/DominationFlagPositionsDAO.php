<?php


namespace mine_deep_rock\dao;


use mine_deep_rock\DataFolderPath;
use pocketmine\level\Position;

class DominationFlagPositionsDAO
{
    static function init() {
        if (!file_exists(DataFolderPath::DominationFlagPositions)) {
            mkdir(DataFolderPath::DominationFlagPositions);
        }
    }

    static function isExist(string $mapName): bool {
        return file_exists(DataFolderPath::DominationFlagPositions . $mapName . ".json");
    }

    static function add(string $mapName): void {
        file_put_contents(DataFolderPath::PlayerStatus . $mapName . ".json", json_encode([]));
    }

    static function get(string $mapName): array {
        if (!file_exists(DataFolderPath::PlayerStatus . $mapName . ".json")) return [];

        $data = json_decode(file_get_contents(DataFolderPath::DominationFlagPositions . $mapName . ".json"), true);
        return array_map(function (array $position) {
            return new Position(
                $position["x"],
                $position["y"],
                $position["z"]);
        }, $data);
    }

    static function update(string $mapName, array $flagPositions): void {
        file_put_contents(DataFolderPath::DominationFlagPositions . $mapName . ".json", json_encode($flagPositions));
    }
}