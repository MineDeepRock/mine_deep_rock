<?php

/*
Serviceから呼び出される。
DBとのやり取りのみ行う。
Serviceが細かな処理をし、Repositoryはできるだけ簡素にする。
 */
abstract class Repository
{
    protected $db;

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
}