<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\dao\PlayerStatusDAO;
use pocketmine\Player;

class InitEffectsPMMPService
{
    static function execute(Player $player): void {
        $player->removeAllEffects();

        $militaryDepartment = PlayerStatusDAO::get($player->getName());
        foreach ($militaryDepartment->getMilitaryDepartment()->getEffectInstances() as $effectInstance) {
            $player->addEffect($effectInstance);
        }
    }
}