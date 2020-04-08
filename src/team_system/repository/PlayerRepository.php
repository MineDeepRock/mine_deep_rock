<?php


namespace team_system\repository;


use mysqli;
use team_system\models\Player;
use team_system\models\TeamId;

class PlayerRepository
{
    private $db;

    public function __construct() {

        //sql jsonへのパス
        $jsonData = file_get_contents("D:\pmmp\plugins\mine_deep_rock\sql.json");
        $decodedJson = json_decode($jsonData, true);

        $host = $decodedJson["host"];
        $user_name = $decodedJson["user_name"];
        $password = $decodedJson["password"];
        $db_name = $decodedJson["db_name"];

        $this->db = new mysqli($host, $user_name, $password, $db_name);

        if ($this->db->connect_error) {
            $sql_error = $this->db->connect_error;
            error_log($sql_error);
            die($sql_error);
        }
    }

    public function register(Player $player) {
        $result = $this->db->query("INSERT INTO players(name) VALUES('{$player->getName()}')");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

    public function exists(string $name): bool {
        return $this->getData($name) != null;
    }

    public function getData(string $name): ?Player {
        $result = $this->db->query("SELECT * FROM players WHERE name='{$name}'");
        if ($result->num_rows === 0) {
            return null;
        }
        return Player::fromJson($result->fetch_assoc());
    }

    public function updateBelongTeamId(string $name, TeamId $teamId): void {
        $result = $this->db->query("UPDATE players SET belong_team_id='{$teamId->value()}' WHERE name='{$name}'");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }
}