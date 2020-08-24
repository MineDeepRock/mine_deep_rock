<?php


namespace mine_deep_rock\service;


use mine_deep_rock\model\OneOnOneRequest;
use mine_deep_rock\store\OneOnOneRequestsStore;

class SendOneOnOneRequestService
{
    static function execute(string $ownerName, string $receiverName): bool {
        $request = OneOnOneRequest::create($ownerName, $receiverName);
        if (OneOnOneRequestsStore::findByOwnerName($request->getOwnerName()) !== null) return false;
        if (OneOnOneRequestsStore::findByReceiverName($request->getOwnerName()) !== null) return false;

        if (OneOnOneRequestsStore::findByOwnerName($request->getReceiverName()) !== null) return false;
        if (OneOnOneRequestsStore::findByReceiverName($request->getReceiverName()) !== null) return false;

        OneOnOneRequestsStore::add($request);
        return true;
    }
}