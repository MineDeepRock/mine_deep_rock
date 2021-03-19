<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\GunRecordDAO;
use mine_deep_rock\dao\PlayerEquipmentsDAO;
use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\GunRecord;
use mine_deep_rock\model\PlayerEquipments;
use mine_deep_rock\pmmp\event\UpdatedPlayerStatusEvent;

class SelectSubGunService
{
    static function execute(string $name, string $gunName, int $scopeMagnification): void {
        $equipments = PlayerEquipmentsDAO::get($name);
        $militaryDepartment = $equipments->getMilitaryDepartment();
        PlayerEquipmentsDAO::update(new PlayerEquipments(
                $name,
                $militaryDepartment,
                $equipments->getMainGunName(),
                $gunName,
                $equipments->getSelectedSkills())
        );

        $gunRecord = GunRecordDAO::get($name, $gunName);
        if ($gunRecord->getScopeMagnification() !== $scopeMagnification) {
            GunRecordDAO::update($name, new GunRecord($gunRecord->getName(), $gunRecord->getKillCount(), $scopeMagnification));
        }

        $status = PlayerStatusDAO::get($name);
        $event = new UpdatedPlayerStatusEvent($status);
        $event->call();
    }
}