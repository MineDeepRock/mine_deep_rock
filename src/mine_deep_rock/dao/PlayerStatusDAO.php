<?php


namespace mine_deep_rock\dao;


use mine_deep_rock\adapter\PlayerStatusJsonAdapter;
use mine_deep_rock\DataFolderPath;
use mine_deep_rock\model\PlayerStatus;

class PlayerStatusDAO
{
    static function init() {
        if (!file_exists(DataFolderPath::PlayerStatus)) {
            mkdir(DataFolderPath::PlayerStatus);
        }
    }

    static function isExist(string $name):bool {
        return file_exists(DataFolderPath::PlayerStatus . $name . ".json");
    }

    static function save(PlayerStatus $playerStatus): void {
        $data = json_encode(PlayerStatusJsonAdapter::encode($playerStatus));
        file_put_contents(DataFolderPath::PlayerStatus . $playerStatus->getName() . ".json", $data);
    }

    static function get(string $name): ?PlayerStatus {
        if (!file_exists(DataFolderPath::PlayerStatus . $name . ".json")) return null;

        $data = json_decode(file_get_contents(DataFolderPath::PlayerStatus . $name . ".json"), true);
        return PlayerStatusJsonAdapter::decode($data);
    }

    static function update(PlayerStatus $playerStatus): void {
        file_put_contents(DataFolderPath::PlayerStatus . $playerStatus->getName() . ".json", json_encode(PlayerStatusJsonAdapter::encode($playerStatus)));
    }
}