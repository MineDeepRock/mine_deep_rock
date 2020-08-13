<?php


namespace mine_deep_rock\service;


use mine_deep_rock\model\PlayerGameStatus;
use mine_deep_rock\store\PlayerGameStatusStore;

class UpdatePlayerGameStatusIsResuscitated
{
    static function execute(string $name):void {
        $playerGameStatus = PlayerGameStatusStore::findByName($name);

        PlayerGameStatusStore::update(new PlayerGameStatus(
            $playerGameStatus->getName(),
            !$playerGameStatus->isResuscitated(),
            $playerGameStatus->getKillCountInGame()
        ));
    }
}