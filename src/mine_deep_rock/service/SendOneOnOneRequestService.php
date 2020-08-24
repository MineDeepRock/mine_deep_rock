<?php


namespace mine_deep_rock\service;


use mine_deep_rock\model\OneOnOneRequest;
use mine_deep_rock\store\OneOnOneRequestsStore;
use team_game_system\model\Score;

class SendOneOnOneRequestService
{
    static function execute(string $ownerName, string $receiverName, ?string $mapName, ?Score $maxScore, ?int $timeLimit = 600): bool {
        if ($mapName === null) {
            //TODO:実装
        }

        //知識:リクエストは一つしか送れない
        $request = OneOnOneRequest::create($ownerName, $receiverName, $mapName, $maxScore, $timeLimit);
        if (OneOnOneRequestsStore::findByOwnerName($request->getOwnerName()) !== null) return false;

        OneOnOneRequestsStore::add($request);
        return true;
    }
}