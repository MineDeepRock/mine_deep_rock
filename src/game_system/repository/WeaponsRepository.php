<?php


namespace game_system\repository;


use game_system\model\Weapon;
use Repository;

class WeaponsRepository extends Repository
{
    public function getOwnWeapons(string $ownerName): array {
        $result = $this->db->query("SELECT * FROM weapons WHERE owner_name='{$ownerName}'");

        $weapons = [];

        if ($result->num_rows === 0) {
            return $weapons;
        }
        if ($result->num_rows === 1) {
            return [Weapon::fromJson($result->fetch_assoc())];
        }

        while ($row = $result->fetch_assoc())
            array_push($weapons, Weapon::fromJson($row));

        return $weapons;
    }

    public function getWeapon(string $ownerName, string $weaponName): Weapon {
        $result = $this->db->query("SELECT * FROM weapons WHERE name='{$weaponName}' AND owner_name='{$ownerName}'");

        if ($result->num_rows === 0) {
            return null;
        }

        return Weapon::fromJson($result->fetch_assoc());
    }

    public function register(string $ownerName, string $weaponName): void {
        $result = $this->db->query("INSERT INTO weapons(name,owner_name) VALUES('{$weaponName}','{$ownerName}')");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

    public function addKillCount(string $ownerName, string $weaponName): void {
        $result = $this->db->query("UPDATE weapons SET kill_count=kill_count+1 WHERE name='{$weaponName}' AND owner_name='{$ownerName}'");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }
}