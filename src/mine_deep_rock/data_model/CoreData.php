<?php


namespace mine_deep_rock\data_model;


use pocketmine\math\Vector3;

class CoreData
{
    /**
     * @var string
     */
    private $teamColor;
    /**
     * @var Vector3
     */
    private $coordinate;

    public function __construct(string $teamColor, Vector3 $coordinate) {
        $this->teamColor = $teamColor;
        $this->coordinate = $coordinate;
    }

    /**
     * @return string
     */
    public function getTeamColor(): string {
        return $this->teamColor;
    }

    /**
     * @return Vector3
     */
    public function getCoordinate(): Vector3 {
        return $this->coordinate;
    }
}