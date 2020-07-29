<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\Player;

class SendLobbyItemsPMMPService
{
    static function execute(Player $player): void {
        $player->getInventory()->setContents([
            //TODO:ロビーでのアイテム
        ]);
    }
}