<?php


namespace game_system\repository;


use game_system\model\Weapon;
use Repository;

class WeaponsRepository extends Repository
{
    public function getOwnWeapons(string $ownerName): array {
        $result = $this->db->query("SELECT * FROM weapons WHERE owner_name='{$ownerName}'");
        $weapons = array_map(function($weapon){
            return Weapon::fromJson($weapon);
        },$result->fetch_assoc());

        return $weapons;
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
        $result = $this->db->query("UPDATE weapons SET kill_count+1 WHERE name='{$weaponName}' AND owner_name='{$ownerName}'");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }
}