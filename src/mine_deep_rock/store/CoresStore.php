<?php


namespace mine_deep_rock\store;


use mine_deep_rock\model\Core;
use team_game_system\model\GameId;

class CoresStore
{

    /**
     * @var Core[]
     */
    private static $cores = [];

    static function add(Core $flag): void {
        self::$cores[] = $flag;
    }

    static function getAll(): array {
        return self::$cores;
    }

    /**
     * @param GameId $gameId
     * @return Core[]
     */
    static function findByGameId(GameId $gameId): array {
        $result = [];

        foreach (self::$cores as $core) {
            if ($core->getGameId()->equals($gameId)) {
                $result[] = $core;
            }
        }
        return $result;
    }

    static function delete(GameId $gameId): void {
        foreach (self::$cores as $key => $core) {
            if ($core->getGameId()->equals($gameId)) {
                unset(self::$cores[$key]);
            }
        }

        self::$cores = array_values(self::$cores);
    }

    static function deleteOne(Core $targetCore): void {
        foreach (self::$cores as $key => $core) {
            if ($core->getGameId()->equals($targetCore->getGameId()) && $core->getPosition()->equals($targetCore->getPosition())) {
                unset(self::$cores[$key]);
            }
        }

        self::$cores = array_values(self::$cores);
    }

    static function update(Core $core) {
        self::delete($core->getGameId());
        self::add($core);
    }
}