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
     * @var CoreData[]
     */
    private $coreDataList;

    public function __construct(string $mapName, array $coreDataList) {
        $this->mapName = $mapName;
        $this->coreDataList = $coreDataList;
    }

    /**
     * @return string
     */
    public function getMapName(): string {
        return $this->mapName;
    }

    /**
     * @return CoreData[]
     */
    public function getCoreDataList(): array {
        return $this->coreDataList;
    }
}