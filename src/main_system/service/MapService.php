<?php


namespace main_system\service;


use main_system\models\Map;
use Service;

class MapService extends Service
{
    private $maps;

    public function __construct() {
        $jsonData = file_get_contents("../data/MapsData.json");
        $decodedJson = json_decode($jsonData, true);

        $this->maps = array_map(function ($map) {
            return Map::fromJson($map);
        }, $decodedJson["maps"]);
    }

    //TODO:
    public function randomUse(): Map {

    }

    //TODO:
    public function use(): Map {

    }

    //TODO:
    public function return(Map $map): void {

    }

    //TODO:
    private function canUseMaps(): array {

    }
}