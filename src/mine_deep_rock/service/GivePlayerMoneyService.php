<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\PlayerStatus;

class GivePlayerMoneyService
{
    static function execute(string $name, int $amount): void {
        $status = PlayerStatusDAO::get($name);
        PlayerStatusDAO::update(new PlayerStatus(
                $status->getName(),
                $status->getMilitaryDepartment(),
                $status->getMainGunName(),
                $status->getSubGunName(),
                $status->getOwningSkills(),
                $status->getSelectedSkills(),
                $status->getMoney() + $amount)
        );
    }
}