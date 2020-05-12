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

        $host = "118.27.8.40";
        $user_name = "mine_deep_rock_user";
        $password = "mine_deep_rock";
        $db_name = "mine_deep_rock";

        $this->db = new mysqli($host, $user_name, $password, $db_name);

        if ($this->db->connect_error) {
            $sql_error = $this->db->connect_error;
            error_log($sql_error);
            die($sql_error);
        }
    }
}