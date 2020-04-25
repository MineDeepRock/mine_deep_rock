<?php


namespace game_system\repository;


use game_system\model\User;
use Repository;

class UsersRepository extends Repository
{
    public function exists(string $userName): bool {
        return $this->getUserData($userName) !== null;
    }

    public function userLogin(string $userName): void {
        if (!$this->exists($userName)) {
            $result = $this->db->query("INSERT INTO users(name) VALUES('{$userName}')");

            if (!$result) {
                $sql_error = $this->db->error;
                error_log($sql_error);
                die($sql_error);
            }
        }
    }

    public function getUserData(string $userName): ?User {
        $result = $this->db->query("SELECT * FROM users WHERE name='{$userName}'");
        if ($result->num_rows === 0) {
            return null;
        }
        return User::fromJson($result->fetch_assoc());
    }

    public function getParticipants(string $gameId): array {
        $result = $this->db->query("SELECT * FROM users WHERE participated_game_id='{$gameId}'");
        if ($result->num_rows === 0) {
            return [];
        }

        $users = array_map(function ($value) {
            return User::fromJson($value);
        }, $result->fetch_assoc());

        return $users;
    }

    public function joinTeam(string $userName, string $teamId, string $gameId): void {
        $result = $this->db->query("UPDATE users SET belong_team_id='{$teamId}',last_belong_team_id='{$teamId}',participated_game_id='{$gameId}' WHERE name='{$userName}'");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

    public function quitTeam(string $userName) {
        $result = $this->db->query("UPDATE users SET belong_team_id=NULL,participated_game_id=NULL WHERE name='{$userName}'");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

    public function addWinCount(string $userName): void {
        $result = $this->db->query("UPDATE users SET win_count=win_count+1 WHERE name='{$userName}'");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

    public function addMoney(string $userName,int $value): void {
        $result = $this->db->query("UPDATE users SET money=money+{$value} WHERE name='{$userName}'");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }
    public function spendMoney(string $userName,int $value): void {
        $result = $this->db->query("UPDATE users SET money=money-{$value} WHERE name='{$userName}'");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }
}