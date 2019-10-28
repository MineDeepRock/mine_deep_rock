<?php

namespace team_system\repository;

use PDO;
use PDOException;
use team_system\models\Team;

class TeamRepository
{
    private $db;

    public function __construct()
    {
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'mine_deep_rock');
        define('DB_USER', 'postgres');
        define('DB_PASSWORD', 'postgres');

        error_reporting(E_ALL & ~E_NOTICE);

        try {
            $this->db = new PDO('pgsql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function create(Team $team)
    {

        $id = $team->getId();
        $ownerName = $team->getOwner()->getName();

        $this->db->query("insert into teams (id, owner_name) values ('db685e94a017', 'aaaa')");

        //TODO:データ保存処理
    }

}