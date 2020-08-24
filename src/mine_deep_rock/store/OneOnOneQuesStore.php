<?php


namespace mine_deep_rock\store;



use mine_deep_rock\model\OneOnOneQue;

class OneOnOneQuesStore
{

    /**
     * @var OneOnOneQue[]
     */
    private static $ques = [];

    static function add(OneOnOneQue $gameId): void {
        self::$ques[] = $gameId;
    }

    static function getAll(): array {
        return self::$ques;
    }

    static function findByOwnerName(string $name): ?OneOnOneQue {
        foreach (self::$ques as $que) {
            if ($que->getOwnerName() === $name) {
                return $que;
            }
        }
        return null;
    }

    static function findByReceiverName(string $name): ?OneOnOneQue {
        foreach (self::$ques as $que) {
            if ($que->getReceiverName() === $name) {
                return $que;
            }
        }
        return null;
    }

    static function delete(OneOnOneQue $targetQue): void {
        foreach (self::$ques as $key => $que) {
            if ($que->getOwnerName() === $targetQue->getOwnerName()) {
                unset(self::$ques[$key]);
            }
        }

        self::$ques = array_values(self::$ques);
    }
}