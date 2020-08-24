<?php


namespace mine_deep_rock\service;


use mine_deep_rock\store\OneOnOneRequestsStore;

class DenyOnOnOneRequestService
{
    static function execute(string $receiverName): bool {
        $request = OneOnOneRequestsStore::findByReceiverName($receiverName);
        if ($request === null) return false;

        OneOnOneRequestsStore::delete($request);
        return true;
    }
}