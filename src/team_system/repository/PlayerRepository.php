<?php


namespace team_system\repository;


use mysqli;
use Repository;
use team_system\models\Player;
use team_system\models\TeamId;

class PlayerRepository extends Repository
{
    public function init(Player $player) {
        $playerName = $player->getName();
        if ($this->exists($playerName)) {
            $result = $this->db->query("DELETE FROM players WHERE name = '{$playerName}'");

            if (!$result) {
                $sql_error = $this->db->error;
                error_log($sql_error);
                die($sql_error);
            }
        }
        $result = $this->db->query("INSERT INTO players(name) VALUES('{$playerName}')");

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

    /**
     * @param string $name
     * @param TeamId|null $teamId
     */
    public function updateBelongTeamId(string $name, ?TeamId $teamId): void {
        $newTeamId = $teamId == null ? null : $teamId->value();

        $result = $this->db->query("UPDATE players SET belong_team_id='{$newTeamId}' WHERE name='{$name}'");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }
}