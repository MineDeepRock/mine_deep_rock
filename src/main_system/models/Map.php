<?php


namespace main_system\models;


class Map
{
    private $startArea;
    private $goalArea;

    private $isUsing;
    private $usingTeamId;

    public function __construct(Area $startArea, Area $goalArea) {

        $this->startArea = $startArea;
        $this->goalArea = $goalArea;
    }

    public function clear():void {
        $this->isUsing = false;
        $this->usingTeamId = null;
    }

    public function use(string $usingTeamId):void {
        $this->isUsing = true;
        $this->usingTeamId = $usingTeamId;
    }

    /**
     * @return mixed
     */
    public function isUsing(): bool {
        return $this->isUsing;
    }

    public static function fromJson(array $json): Map {
        $startArea = Area::fromJson($json["start_area"]);
        $endArea = Area::fromJson($json["end_area"]);

        return new Map($startArea, $endArea);
    }
}


class Area
{
    private $startPosX;
    private $startPosY;

    private $endPosX;
    private $endPosY;

    public function __construct(int $startPosX, int $startPosY, int $endPosX, int $endPosY) {
        $this->startPosX = $startPosX;
        $this->startPosY = $startPosY;
        $this->endPosX = $endPosX;
        $this->endPosY = $endPosY;
    }

    public static function fromJson(array $json): Area {
        $startPosX = $json["start_pos_x"];
        $startPosY = $json["start_pos_y"];
        $endPosX = $json["end_pos_x"];
        $endPosY = $json["end_pos_y"];

        return new Area($startPosX, $startPosY, $endPosX, $endPosY);
    }
}