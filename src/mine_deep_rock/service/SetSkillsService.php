<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\PlayerStatus;

class SetSkillsService
{
    static function execute(string $name, array $skills): void {
        $status = PlayerStatusDAO::get($name);
        PlayerStatusDAO::update(new PlayerStatus(
                $name,
                $status->getMilitaryDepartment(),
                $status->getMainGunName(),
                $status->getSubGunName(),
                $status->getOwningSkills(),
                $skills,
                $status->getMoney())
        );
    }
}