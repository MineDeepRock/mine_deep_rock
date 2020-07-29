<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\Player;

class InitTDMEquipmentsPMMPService
{
    static function execute(Player $player): void {
        //TODO:装備の初期化
        $player->getInventory()->setContents([]);
    }
}