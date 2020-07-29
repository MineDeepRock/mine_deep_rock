<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\PlayerStatus;

class SelectSubGunService
{
    static function execute(string $name, string $gunName): void {
        $status = PlayerStatusDAO::get($name);
        $militaryDepartment = $status->getMilitaryDepartment();
        PlayerStatusDAO::update(new PlayerStatus($name, $militaryDepartment, $status->getMainGunName(), $gunName));
    }
}