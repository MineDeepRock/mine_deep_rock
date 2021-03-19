<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\PlayerEquipmentsDAO;
use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\PlayerEquipments;
use mine_deep_rock\model\skill\normal\NormalSkill;
use mine_deep_rock\pmmp\event\UpdatedPlayerStatusEvent;
use mine_deep_rock\store\MilitaryDepartmentsStore;

class SelectMilitaryDepartmentService
{
    static function execute(string $name, string $militaryDepartmentName): void {
        $equipments = PlayerEquipmentsDAO::get($name);
        $militaryDepartment = MilitaryDepartmentsStore::get($militaryDepartmentName);
        $skills = [];
        if ($equipments->getMilitaryDepartment()->getName() !== $militaryDepartmentName) {
            foreach ($equipments->getSelectedSkills() as $selectedSkill) {
                if ($selectedSkill instanceof NormalSkill) {
                    $skills[] = $selectedSkill;
                }
            }
        }

        PlayerEquipmentsDAO::update(new PlayerEquipments(
                $name,
                $militaryDepartment,
                $militaryDepartment->getDefaultGunName(),
                $equipments->getSubGunName(),
                $equipments->getSelectedSkills())
        );

        $status = PlayerStatusDAO::get($name);
        $event = new UpdatedPlayerStatusEvent($status);
        $event->call();
    }
}