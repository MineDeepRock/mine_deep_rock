<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\PlayerStatus;
use mine_deep_rock\model\skill\normal\NormalSkill;
use mine_deep_rock\store\MilitaryDepartmentsStore;

class SelectMilitaryDepartmentService
{
    static function execute(string $name, string $militaryDepartmentName): void {
        $status = PlayerStatusDAO::get($name);
        $militaryDepartment = MilitaryDepartmentsStore::get($militaryDepartmentName);
        $skills = [];
        if ($status->getMilitaryDepartment()->getName() !== $militaryDepartmentName) {
            foreach ($status->getSelectedSkills() as $selectedSkill) {
                if ($selectedSkill instanceof NormalSkill) {
                    $skills[] = $selectedSkill;
                }
            }
        }

        PlayerStatusDAO::update(new PlayerStatus(
                $name,
                $militaryDepartment,
                $militaryDepartment->getDefaultGunName(),
                $status->getSubGunName(),
                $status->getOwningSkills(),
                $skills,
                $status->getMoney())
        );
    }
}