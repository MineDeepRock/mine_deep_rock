<?php

namespace team_system\repository;

use mysqli;
use team_system\models\Team;

class TeamRepository
{
    private $db;

    public function __construct()
    {

        $jsonData = file_get_contents("./sql.json");
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

    public function create(Team $team)
    {

        $id = $team->getId();
        $owner_name = $team->getOwner()->getName();

        $result = $this->db->query("INSERT INTO teams(id,owner_name) VALUES('{$id}','{$owner_name}')");

        if (!$result) {
            $sql_error = $this->db->error;
            echo 'select failed';
            error_log($sql_error);
            die($sql_error);
        }

        return true;
    }

}