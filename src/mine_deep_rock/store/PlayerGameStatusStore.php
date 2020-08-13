<?php


namespace mine_deep_rock\store;


use mine_deep_rock\model\PlayerGameStatus;

class PlayerGameStatusStore
{

    /**
     * @var PlayerGameStatus[]
     */
    private static $gameStatusList = [];

    static function add(PlayerGameStatus $gameId): void {
        self::$gameStatusList[] = $gameId;
    }

    static function getAll(): array {
        return self::$gameStatusList;
    }

    static function findByName(string $name): ?PlayerGameStatus {
        foreach (self::$gameStatusList as $playerGameStatus) {
            if ($playerGameStatus->getName() === $name) {
                return $playerGameStatus;
            }
        }
        return null;
    }

    static function delete(string $name): void {
        foreach (self::$gameStatusList as $key => $gameStatus) {
            if ($gameStatus->getName() === $name) {
                unset(self::$gameStatusList[$key]);
            }
        }

        self::$gameStatusList = array_values(self::$gameStatusList);
    }

    static function update(PlayerGameStatus $gameStatus) {
        self::delete($gameStatus->getName());
        self::add($gameStatus);
    }

    static function isExist(string $name): bool {
        return self::findByName($name) !== null;
    }
}