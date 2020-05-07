<?php


namespace game_system\model;


use game_system\model\map\Map;
use game_system\model\map\TeamDeathMatchMap;

class TeamDeathMatch extends TwoTeamGame
{
    public function __construct(TeamDeathMatchMap $map) {
        parent::__construct($map);
    }

    public function getMap(): Map {
        return parent::getMap();
    }
}