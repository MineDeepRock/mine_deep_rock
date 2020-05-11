<?php


namespace game_system\repository;


use game_system\model\GameScore;
use Repository;

class GameScoresRepository extends Repository
{

    public function getScores(string $gameId): array {
        $result = $this->db->query("SELECT * FROM scores WHERE game_id='{$gameId}'");

        $scores = [];

        if ($result->num_rows === 0) {
            return $scores;
        }
        if ($result->num_rows === 1) {
            return [GameScore::fromJson($result->fetch_assoc())];
        }

        while ($row = $result->fetch_assoc())
            array_push($scores, GameScore::fromJson($row));

        return $scores;
    }

    public function getUserScores(string $name): array {
        $result = $this->db->query("SELECT * FROM scores WHERE name='{$name}'");

        $scores = [];

        if ($result->num_rows === 0) {
            return $scores;
        }
        if ($result->num_rows === 1) {
            return [GameScore::fromJson($result->fetch_assoc())];
        }

        while ($row = $result->fetch_assoc())
            array_push($scores, GameScore::fromJson($row));

        return $scores;
    }

    public function getUserScore(string $name, string $gameId): GameScore {
        $result = $this->db->query("SELECT * FROM scores WHERE name='{$name}' AND game_id='{$gameId}'");

        return GameScore::fromJson($result->fetch_assoc());
    }

    public function addScore(string $name, string $gameId): void {
        $result = $this->db->query("INSERT INTO scores(name,game_id) VALUES('{$name}','{$gameId}')");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

    public function addKillCount(string $name, string $gameId): void {
        $result = $this->db->query("UPDATE scores SET kill_count=kill_count+1 WHERE name='{$name}' AND game_id='{$gameId}'");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

    public function addPoint(string $name, string $gameId, int $value): void {
        $result = $this->db->query("UPDATE scores SET point=point+{$value} WHERE name='{$name}' AND game_id='{$gameId}'");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }
}