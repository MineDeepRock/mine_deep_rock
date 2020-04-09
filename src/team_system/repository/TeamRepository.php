<?php

namespace team_system\repository;

use mysqli;
use Repository;
use team_system\models\Team;

//こっちは条件分岐をほとんど書かない。
//実行だけにしたい。
//サービスが条件分岐をやる！
class TeamRepository extends Repository
{
    /**
     * @return array
     */
    public function getAll(): array {
        $result = $this->db->query("SELECT * FROM teams;");

        return $result->fetch_assoc();
    }

    public function searchAtId(String $id): ?Team {

        $result = $this->db->query("SELECT * FROM teams WHERE id='{$id}'");
        if ($result->num_rows === 0) {
            return null;
        }
        return Team::fromJson($result->fetch_assoc());
    }

    public function searchAtOwnerName(String $ownerName): ?Team {

        $result = $this->db->query("SELECT * FROM teams WHERE owner_name='{$ownerName}'");
        if ($result->num_rows === 0) {
            return null;
        }
        return Team::fromJson($result->fetch_assoc());
    }

    /**
     * @param String $ownerName
     * @return bool
     */
    public function contain(String $ownerName): bool {
        return $this->searchAtOwnerName($ownerName) != null;
    }

    /**
     * @param string $teamId
     * @param string $owner_name
     */
    public function create(string $teamId, string $owner_name): void {
        $result = $this->db->query("INSERT INTO teams(id,owner_name) VALUES('{$teamId}','{$owner_name}')");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

    /**
     * @param string $playerName
     * @param Team $team
     */
    public function join(string $playerName, Team $team): void {
        $id = $team->getId()->value();
        $coworkerSlot = $team->nextEmptySlot();

        $result = $this->db->query("UPDATE teams SET {$coworkerSlot}='{$playerName}' WHERE id='{$id}'");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

    /**
     * @param string $playerName
     * @param Team $team
     */
    public function quit(string $playerName, Team $team): void {
        $id = $team->getId()->value();
        $coworkerSlot = $team->isWherePlayerSlot($playerName);
        $result = $this->db->query("UPDATE teams SET {$coworkerSlot}=NULL WHERE id='{$id}'");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

    public function yieldOwner(String $currentOwnerName, string $nextOwnerName, string $coworkerSlot): void {
        $setOwnerToCoworker = $this->db->query("UPDATE teams SET {$coworkerSlot}='{$currentOwnerName->getName()}' WHERE owner_name='{$currentOwnerName}'");
        if (!$setOwnerToCoworker) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }

        $exchangeOwnerResult = $this->db->query("UPDATE teams SET owner_name='{$nextOwnerName}' WHERE owner_name='{$currentOwnerName}'");
        if (!$exchangeOwnerResult) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

    public function delete(String $ownerName): void {
        $result = $this->db->query("DELETE FROM teams WHERE owner_name='{$ownerName}'");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

}