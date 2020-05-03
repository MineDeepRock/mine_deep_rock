<?php


namespace game_system\model;


use ValueObject;

class Weapon extends ValueObject
{
    private $name;
    private $scope;

    private $ownerName;
    private $killCount;

    public function __construct(string $ownerName, string $name, string $scope, $killCount = 0) {
        $this->name = $name;
        $this->scope = $scope;
        $this->ownerName = $ownerName;
        $this->killCount = $killCount;
    }

    public static function fromJson(array $json): Weapon {
        $ownerName = $json["owner_name"];
        $scope = $json["scope"];
        $name = $json["name"];
        $killCount = $json["kill_count"];

        return new Weapon($ownerName, $name, $scope, $killCount);
    }

    /**
     * @return int
     */
    public function getKillCount(): int {
        return $this->killCount;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getScope(): string {
        return $this->scope;
    }
}