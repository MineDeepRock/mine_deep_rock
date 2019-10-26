<?php

namespace team_system\service;

use team_system\models\Player;
use team_system\models\Team;

class TeamService implements iTeamService
{
    public static function create(Player $owner){

        $createdTeam = new Team($owner);

        //TODO:データ保存処理
        return $createdTeam;
    }

    public static function breakup()
    {
        // TODO: Implement breakup() method.
    }
}

interface iTeamService {
    public static function create(Player $owner);
    public static function breakup();
}