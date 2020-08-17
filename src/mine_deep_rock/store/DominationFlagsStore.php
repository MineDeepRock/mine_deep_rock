<?php


namespace mine_deep_rock\store;


use mine_deep_rock\model\DominationFlag;
use team_game_system\model\GameId;

class DominationFlagsStore
{

    /**
     * @var DominationFlag[]
     */
    private static $flags = [];

    static function add(DominationFlag $flag): void {
        self::$flags[] = $flag;
    }

    static function getAll(): array {
        return self::$flags;
    }

    /**
     * @param GameId $gameId
     * @return DominationFlag[]
     */
    static function findByGameId(GameId $gameId): array {
        $result = [];

        foreach (self::$flags as $flag) {
            if ($flag->getGameId()->equals($gameId)) {
                $result[] = $flag;
            }
        }
        return $result;
    }

    static function delete(GameId $gameId): void {
        foreach (self::$flags as $key => $flag) {
            if ($flag->getGameId()->equals($gameId)) {
                unset(self::$flags[$key]);
            }
        }

        self::$flags = array_values(self::$flags);
    }

    static function deleteOne(DominationFlag $targetFlag): void {
        foreach (self::$flags as $key => $flag) {
            if ($flag->getGameId()->equals($targetFlag->getGameId()) && $flag->getName() === $targetFlag->getName()) {
                unset(self::$flags[$key]);
            }
        }

        self::$flags = array_values(self::$flags);
    }

    static function update(DominationFlag $flag) {
        self::delete($flag->getGameId());
        self::add($flag);
    }
}