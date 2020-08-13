<?php


namespace mine_deep_rock\service;


use mine_deep_rock\model\PlayerGameStatus;
use mine_deep_rock\store\PlayerGameStatusStore;

class AddKillCountInGameService
{
    static function execute(string $name): void {
        $status = PlayerGameStatusStore::findByName($name);
        PlayerGameStatusStore::update(new PlayerGameStatus(
            $status->getName(),
            $status->isResuscitated(),
            $status->getKillCountInGame() + 1
        ));
    }
}