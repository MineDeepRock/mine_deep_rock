<?php


namespace mine_deep_rock\service;


use mine_deep_rock\model\OneOnOneRequest;
use mine_deep_rock\store\OneOnOneRequestsStore;

class SendOneOnOneRequestService
{
    static function execute(string $ownerName, string $receiverName): bool {
        //知識:リクエストは一つしか送れない
        $request = OneOnOneRequest::create($ownerName, $receiverName);
        if (OneOnOneRequestsStore::findByOwnerName($request->getOwnerName()) !== null) return false;

        OneOnOneRequestsStore::add($request);
        return true;
    }
}