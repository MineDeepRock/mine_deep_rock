<?php


namespace mine_deep_rock;


use team_game_system\model\GameType;

class GameTypeList
{
    static function TDM(): GameType {
        return new GameType("TDM");
    }

    static function Domination(): GameType {
        return new GameType("Domination");
    }
}