<?php

namespace team_system\repository;

use mysqli;
use team_system\models\Player;
use team_system\models\Team;
use team_system\models\TeamId;

//こっちは条件分岐をほとんど書かない。
//実行だけにしたい。
//サービスが条件分岐をやる！
class TeamRepository
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

    /**
     * @return array
     */
    public function getAll(): array {
        $result = $this->db->query("SELECT * FROM teams;");

        return $result->fetch_assoc();
    }

    /**
     * @param String $ownerName
     * @return Team|null
     */
    public function search(String $ownerName): ?Team {
        if (!$this->contain($ownerName)) {
            return null;
        }
        $result = $this->db->query("SELECT * FROM teams WHERE owner_name='{$ownerName}'");
        return Team::fromJson($result->fetch_assoc());
    }

    /**
     * @param String $ownerName
     * @return bool
     */
    public function contain(String $ownerName): bool {
        $result = $this->db->query("SELECT * FROM teams WHERE owner_name = '{$ownerName}';");
        print $result->num_rows;

        return $result->num_rows == 1;
    }

    /**
     * @param Team $team
     */
    public function create(Team $team): void {

        $id = $team->getId()->value();
        $owner_name = $team->getOwner()->getName();

        $result = $this->db->query("INSERT INTO teams(id,owner_name) VALUES('{$id}','{$owner_name}')");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

    /**
     * @param Player $sender
     * @param Team $team
     */
    public function join(Player $sender, Team $team): void {
        $id = $team->getId()->value();
        $senderName = $sender->getName();
        $coworkerSlot = $team->setToEmptySlot($senderName);

        $result = $this->db->query("UPDATE teams SET {$coworkerSlot}='{$senderName}' WHERE id='{$id}'");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

    /**
     * @param Player $sender
     * @param Team $team
     */
    public function quit(Player $sender, Team $team): void {
        $id = $team->getId()->value();
        $senderName = $sender->getName();
        $coworkerSlot = $team->searchPlayerSlot($senderName);

        $result = $this->db->query("UPDATE teams SET {$coworkerSlot}=NULL WHERE id='{$id}'");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

}