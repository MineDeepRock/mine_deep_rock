<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\model\MilitaryDepartment;
use mine_deep_rock\service\SelectMilitaryDepartmentService;
use pocketmine\Player;

class ResortAsSentryPMMPService
{
    static function execute(Player $player): void {
        SelectMilitaryDepartmentService::execute($player->getName(), MilitaryDepartment::Sentry);

        InitEquipmentsPMMPService::execute($player);
        InitEffectsPMMPService::execute($player);
    }
}