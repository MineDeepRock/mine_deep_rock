<?php


namespace mine_deep_rock\data_model;


use pocketmine\level\Position;

class CorePvPMapData
{
    /**
     * @var string
     */
    private $mapName;
    /**
     * @var CandidateCorePositionsGroup[]
     */
    private $candidateCorePositionsGroups;

    public function __construct(string $mapName, array $candidateCorePositionsGroups) {
        $this->mapName = $mapName;
        $this->candidateCorePositionsGroups = $candidateCorePositionsGroups;
    }

    /**
     * @return string
     */
    public function getMapName(): string {
        return $this->mapName;
    }

    /**
     * @return CandidateCorePositionsGroup[]
     */
    public function getCandidateCorePositionsGroups(): array {
        return $this->candidateCorePositionsGroups;
    }
}