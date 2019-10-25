<?php


use main_team_system\models\Player;
use main_team_system\models\Team;

class TeamService implements iTeamService
{
    public function create(Player $owner){

        $createdTeam = new Team($owner);

        //TODO:データ保存処理
        return $createdTeam;
    }

    public function breakup()
    {
        // TODO: Implement breakup() method.
    }
}

interface iTeamService {
    public function create(Player $owner);
    public function breakup();
}