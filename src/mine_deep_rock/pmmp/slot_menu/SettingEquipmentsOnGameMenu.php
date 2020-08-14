<?php


namespace mine_deep_rock\pmmp\slot_menu;


use mine_deep_rock\pmmp\service\ResortToTDMPMMPService;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class SettingEquipmentsOnGameMenu extends SlotMenu
{
    public function __construct(TaskScheduler $taskScheduler) {
        $menus = [
            new SlotMenuElement(ItemIds::COMPASS, "兵科", 0, function (Player $player) use ($taskScheduler) {
                SlotMenuSystem::send($player, new SelectMilitaryDepartmentMenu($this));
            }),
            new SlotMenuElement(ItemIds::DIAMOND_SWORD, "メインウェポン", 1, function (Player $player) use ($taskScheduler) {
                SlotMenuSystem::send($player, new SelectMainGunTypeMenu($player, $this, $taskScheduler));
            }),
            new SlotMenuElement(ItemIds::IRON_SWORD, "サブウェポン", 2, function (Player $player) use ($taskScheduler) {
                SlotMenuSystem::send($player, new SelectSubGunTypeMenu($this, $taskScheduler));
            }),
            new SlotMenuElement(ItemIds::EMERALD, "再出撃", 8, function (Player $player) {
                ResortToTDMPMMPService::execute($player, null, true);
            }),
        ];
        parent::__construct($menus);
    }
}