<?php


namespace game_system\model;


use ValueObject;

class Weapon extends ValueObject
{
    private $name;
    private $ownerName;
    private $killCount;

    public function __construct(string $ownerName, string $name, $killCount = 0) {
        $this->name = $name;
        $this->ownerName = $ownerName;
        $this->killCount = $killCount;
    }

    public static function fromJson(array $json) : Weapon {
        $ownerName = $json["owner_name"];
        $name = $json["name"];
        $killCount = $json["kill_count"];

        return new Weapon($ownerName,$name,$killCount);
    }
}