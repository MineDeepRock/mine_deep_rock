<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\GunRecordDAO;
use mine_deep_rock\model\GunRecord;

class AddKillCountToGunRecordService
{
    static function execute(string $playerName, string $gunName): void {
        $record = GunRecordDAO::get($playerName, $gunName);
        if ($record !== null) {
            GunRecordDAO::update($playerName, new GunRecord(
                $record->getName(),
                $record->getKillCount() + 1,
                $record->getScopeMagnification()
            ));
        }
    }
}