<?php


namespace mine_deep_rock\service;


use mine_deep_rock\model\PlayerGameStatus;
use mine_deep_rock\store\PlayerGameStatusStore;

class ResetPlayerGameStatusService
{
    static function execute(string $name): void {
        PlayerGameStatusStore::update(PlayerGameStatus::asNew($name));
    }
}