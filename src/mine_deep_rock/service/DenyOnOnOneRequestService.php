<?php


namespace mine_deep_rock\service;


use mine_deep_rock\model\OneOnOneRequest;
use mine_deep_rock\store\OneOnOneRequestsStore;

class DenyOnOnOneRequestService
{
    static function execute(OneOnOneRequest $request): bool {
        OneOnOneRequestsStore::delete($request);
        return true;
    }
}