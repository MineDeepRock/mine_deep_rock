<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\PlayerEquipmentsDAO;
use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\PlayerEquipments;
use mine_deep_rock\model\PlayerStatus;

class SetSkillsService
{
    static function execute(string $name, array $skills): void {
        $equipments = PlayerEquipmentsDAO::get($name);
        PlayerEquipmentsDAO::update(new PlayerEquipments(
                $name,
                $equipments->getMilitaryDepartment(),
                $equipments->getMainGunName(),
                $equipments->getSubGunName(),
                $skills)
        );
    }
}