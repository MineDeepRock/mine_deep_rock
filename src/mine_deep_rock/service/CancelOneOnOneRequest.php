<?php


namespace mine_deep_rock\service;


use mine_deep_rock\store\OneOnOneRequestsStore;

class CancelOneOnOneRequest
{
    static function execute(string $ownerName): bool {
        $que = OneOnOneRequestsStore::findByOwnerName($ownerName);
        if ($que === null) return false;

        OneOnOneRequestsStore::delete($que);
        return true;
    }
}