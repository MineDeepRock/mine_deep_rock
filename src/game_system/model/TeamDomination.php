<?php


namespace game_system\model;


use game_system\model\map\Map;
use game_system\model\map\TeamDominationMap;

class TeamDomination extends TwoTeamGame
{
    public function __construct(TeamDominationMap $map) {
        parent::__construct($map);
    }

    public function getMap(): Map {
        return parent::getMap();
    }
}