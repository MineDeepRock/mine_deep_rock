<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\PlayerStatus;
use mine_deep_rock\pmmp\event\UpdatedPlayerStatusEvent;

class SpendMoneyService
{
    static function execute(string $name, int $amount): void {
        $status = PlayerStatusDAO::get($name);
        PlayerStatusDAO::update(new PlayerStatus(
                $name,
                $status->getLevel(),
                $status->getMoney() - $amount,
                $status->getOwningSkills()
            )
        );

        $status = PlayerStatusDAO::get($name);
        $event = new UpdatedPlayerStatusEvent($status);
        $event->call();
    }
}