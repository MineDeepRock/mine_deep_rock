<?php


namespace mine_deep_rock\store;


use team_game_system\model\GameId;

class TDMGameIdsStore
{

    /**
     * @var GameId[]
     */
    private static $ids = [];

    static function add(GameId $gameId): void {
        self::$ids[] = $gameId;
    }

    static function getAll(): array {
        return self::$ids;
    }

    static function delete(GameId $gameId): void {
        foreach (self::$ids as $key => $id) {
            if ($id->equals($gameId)) {
                unset(self::$ids[$key]);
            }
        }

        self::$ids = array_values(self::$ids);
    }
}