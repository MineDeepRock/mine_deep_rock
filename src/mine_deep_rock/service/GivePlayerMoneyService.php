<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\PlayerStatus;

class GivePlayerMoneyService
{
    static function execute(string $name, int $amount): void {
        $status = PlayerStatusDAO::get($name);
        PlayerStatusDAO::update(new PlayerStatus(
                $name,
                $status->getLevel(),
                $status->getMoney() + $amount,
                $status->getOwningSkills()
            )
        );
    }
}