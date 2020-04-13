<?php


namespace main_system\models;


class Map
{
    private $startArea;
    private $goalArea;
    private $veinChoices;

    private $isUsing;
    private $usingTeamId;

    public function __construct(Area $startArea, Area $goalArea, array $veinChoices) {
        $this->startArea = $startArea;
        $this->goalArea = $goalArea;
        $this->veinChoices = $veinChoices;
    }

    public function clear(): void {
        $this->isUsing = false;
        $this->usingTeamId = null;
    }

    public function use(string $usingTeamId): void {
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
        $veins = $json["veins"]["diamonds"];

        $veins = array_map(function (array $vein) {
            return array_map(function(array $oresPosition){
                return new Position($oresPosition[0],$oresPosition[1],$oresPosition[2]);
            },$vein);
        }, $veins);

        return new Map($startArea, $endArea,$veins);
    }
}

class Area
{
    private $startPosition;
    private $endPosition;

    public function __construct(Position $startPosition, Position $endPosition) {
        $this->startPosition = $startPosition;
        $this->endPosition = $endPosition;
    }

    public static function fromJson(array $json): Area {
        $startPosition = new Position($json["start_pos_x"], $json["start_pos_y"], $json["start_pos_z"]);
        $endPosition = new Position($json["end_pos_x"], $json["end_pos_y"], $json["end_pos_z"]);

        return new Area($startPosition, $endPosition);
    }
}

class Position
{
    private $x;
    private $y;
    private $z;

    public function __construct(int $x, int $y, int $z) {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    /**
     * @return int
     */
    public function getX(): int {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY(): int {
        return $this->y;
    }

    /**
     * @return int
     */
    public function getZ(): int {
        return $this->z;
    }
}