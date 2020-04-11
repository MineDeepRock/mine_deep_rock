<?php

namespace team_system\repositories;

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

    public function searchAtLeaderName(String $leaderName): ?Team {

        $result = $this->db->query("SELECT * FROM teams WHERE leader_name='{$leaderName}'");
        if ($result->num_rows === 0) {
            return null;
        }
        return Team::fromJson($result->fetch_assoc());
    }

    /**
     * @param String $leaderName
     * @return bool
     */
    public function contain(String $leaderName): bool {
        return $this->searchAtLeaderName($leaderName) != null;
    }

    /**
     * @param string $teamId
     * @param string $leader_name
     */
    public function create(string $teamId, string $leader_name): void {
        $result = $this->db->query("INSERT INTO teams(id,leader_name) VALUES('{$teamId}','{$leader_name}')");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

    /**
     * @param string $memberName
     * @param Team $team
     */
    public function join(string $memberName, Team $team): void {
        $id = $team->getId()->value();
        $memberSlot = $team->nextEmptySlot();

        $result = $this->db->query("UPDATE teams SET {$memberSlot}='{$memberName}' WHERE id='{$id}'");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

    /**
     * @param string $memberName
     * @param Team $team
     */
    public function quit(string $memberName, Team $team): void {
        $id = $team->getId()->value();
        $memberSlot = $team->getMemberSlot($memberName);
        $result = $this->db->query("UPDATE teams SET {$memberSlot}=NULL WHERE id='{$id}'");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

    public function yieldLeader(String $currentLeaderName, string $nextLeaderName, string $memberSlot): void {
        $makeMemberLeader = $this->db->query("UPDATE teams SET {$memberSlot}='{$currentLeaderName}' WHERE leader_name='{$currentLeaderName}'");
        if (!$makeMemberLeader) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }

        $exchangeLeaderResult = $this->db->query("UPDATE teams SET leader_name='{$nextLeaderName}' WHERE leader_name='{$currentLeaderName}'");
        if (!$exchangeLeaderResult) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

    public function delete(String $leaderName): void {
        $result = $this->db->query("DELETE FROM teams WHERE leader_name='{$leaderName}'");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

}