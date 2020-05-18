<?php


namespace game_system\model;


class SpawnBeacon
{
    private $id;
    private $ownerName;
    private $ownerTeamId;
    private $position;
    private $describe;
    private $isAvailable = true;

    public function __construct(string $ownerName, TeamID $ownerTeamId, Coordinate $position, string $describe = "") {
        $this->id = SpawnBeaconId::asNew();
        $this->ownerName = $ownerName;
        $this->ownerTeamId = $ownerTeamId;
        $this->position = $position;
        $this->describe = $describe;
    }

    /**
     * @return string
     */
    public function getOwnerName(): string {
        return $this->ownerName;
    }

    /**
     * @return TeamID
     */
    public function getOwnerTeamId(): TeamID {
        return $this->ownerTeamId;
    }

    /**
     * @return Coordinate
     */
    public function getPosition(): Coordinate {
        return $this->position;
    }

    /**
     * @return SpawnBeaconId
     */
    public function getId(): SpawnBeaconId {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool {
        return $this->isAvailable;
    }

    /**
     * @param bool $isAvailable
     */
    public function setIsAvailable(bool $isAvailable): void {
        $this->isAvailable = $isAvailable;
    }

    /**
     * @return string
     */
    public function getDescribe(): string {
        return $this->describe;
    }

    /**
     * @param string $describe
     */
    public function setDescribe(string $describe): void {
        $this->describe = $describe;
    }
}