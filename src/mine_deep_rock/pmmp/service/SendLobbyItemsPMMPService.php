<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\pmmp\slot_menu\SettingEquipmentsMenu;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use slot_menu_system\SlotMenuSystem;

class SendLobbyItemsPMMPService
{
    static function execute(Player $player, TaskScheduler $taskScheduler): void {
        SlotMenuSystem::send($player, new SettingEquipmentsMenu($taskScheduler));
    }
}