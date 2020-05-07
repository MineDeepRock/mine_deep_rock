<?php


namespace game_system\pmmp\client;


class TeamDeathMatchClient extends TwoTeamGameClient
{
    public function __construct() {
        parent::__construct("TeamDeathMatch");
    }
}