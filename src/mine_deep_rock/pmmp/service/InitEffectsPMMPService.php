<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\dao\PlayerEquipmentsDAO;
use pocketmine\Player;

class InitEffectsPMMPService
{
    static function execute(Player $player): void {
        $player->removeAllEffects();

        $militaryDepartment = PlayerEquipmentsDAO::get($player->getName())->getMilitaryDepartment();
        foreach ($militaryDepartment->getEffectInstances() as $effectInstance) {
            $player->addEffect($effectInstance);
        }
    }
}