<?php


namespace mine_deep_rock\data_model;


use pocketmine\level\Position;

class CandidateCorePositionsGroup
{
    /**
     * @var Position[]
     */
    private $positions;

    public function __construct(array $positions) {
        $this->positions = $positions;
    }
}