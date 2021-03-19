<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\GunRecordDAO;
use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\GunRecord;
use mine_deep_rock\model\PlayerStatus;

class SelectMainGunService
{
    static function execute(string $name, string $gunName, int $scopeMagnification): void {
        $status = PlayerStatusDAO::get($name);
        PlayerStatusDAO::update(new PlayerStatus(
                $name,
                $status->getLevel(),
                $status->getMoney(),
                $status->getOwningSkills()
            )
        );

        $gunRecord = GunRecordDAO::get($name, $gunName);
        GunRecordDAO::update($name, new GunRecord($gunRecord->getName(), $gunRecord->getKillCount(), $scopeMagnification));
    }
}