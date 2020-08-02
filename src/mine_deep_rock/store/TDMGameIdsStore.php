<?php


namespace mine_deep_rock\store;


use team_game_system\model\GameId;

class TDMGameIdsStore
{

    /**
     * @var GameId[]
     */
    private static $ids = [];
    static function add(GameId $gameId) :void {
        self::$ids[] = $gameId;
    }
    static function getAll():array{
        return self::$ids;
    }
}