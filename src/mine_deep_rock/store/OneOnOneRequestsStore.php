<?php


namespace mine_deep_rock\store;



use mine_deep_rock\model\OneOnOneRequest;

class OneOnOneRequestsStore
{

    /**
     * @var OneOnOneRequest[]
     */
    private static $requests = [];

    static function add(OneOnOneRequest $gameId): void {
        self::$requests[] = $gameId;
    }

    static function getAll(): array {
        return self::$requests;
    }

    static function findByOwnerName(string $name): ?OneOnOneRequest {
        foreach (self::$requests as $request) {
            if ($request->getOwnerName() === $name) {
                return $request;
            }
        }
        return null;
    }

    static function findByReceiverName(string $name): ?OneOnOneRequest {
        foreach (self::$requests as $request) {
            if ($request->getReceiverName() === $name) {
                return $request;
            }
        }
        return null;
    }

    static function delete(OneOnOneRequest $targetQue): void {
        foreach (self::$requests as $key => $request) {
            if ($request->getOwnerName() === $targetQue->getOwnerName()) {
                unset(self::$requests[$key]);
            }
        }

        self::$requests = array_values(self::$requests);
    }
}