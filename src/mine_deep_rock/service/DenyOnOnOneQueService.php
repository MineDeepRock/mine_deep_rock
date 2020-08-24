<?php


namespace mine_deep_rock\service;


use mine_deep_rock\store\OneOnOneQuesStore;

class DenyOnOnOneQueService
{
    static function execute(string $receiverName): bool {
        $que = OneOnOneQuesStore::findByReceiverName($receiverName);
        if ($que === null) return false;

        OneOnOneQuesStore::delete($que);
        return true;
    }
}