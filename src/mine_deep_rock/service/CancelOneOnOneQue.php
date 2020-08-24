<?php


namespace mine_deep_rock\service;


use mine_deep_rock\store\OneOnOneQuesStore;

class CancelOneOnOneQue
{
    static function execute(string $ownerName): bool {
        $que = OneOnOneQuesStore::findByOwnerName($ownerName);
        if ($que === null) return false;

        OneOnOneQuesStore::delete($que);
        return true;
    }
}