<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\GunRecordDAO;
use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\GunRecord;

class BuyGunService
{
    static function execute(string $name, string $gunName) {
        $price = 3500;

        $status = PlayerStatusDAO::get($name);

        foreach (GunRecordDAO::getOwn($name) as $gunRecord) {
            if ($gunRecord->getName() === $gunName) {
                return false;
            }
        }

        if ($status->getMoney() <= $price) {
            return false;
        }

        SpendMoneyService::execute($name, $price);
        GunRecordDAO::add($name, GunRecord::asNew($gunName));
        return true;
    }
}