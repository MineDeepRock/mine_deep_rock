<?php


namespace mine_deep_rock\pmmp\slot_menu;


use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class SettingEquipmentsMenu extends SlotMenu
{
    public function __construct(TaskScheduler $taskScheduler) {
        $menus = [
            new SlotMenuElement(ItemIds::COMPASS, "兵科", 0, function (Player $player) use ($taskScheduler) {
                SlotMenuSystem::send($player, new SelectMilitaryDepartmentMenu($taskScheduler));
            }),
            new SlotMenuElement(ItemIds::DIAMOND_SWORD, "メインウェポン", 1, function (Player $player) use ($taskScheduler) {
                SlotMenuSystem::send($player, new SelectMainGunTypeMenu($player, $taskScheduler));
            }),
            new SlotMenuElement(ItemIds::IRON_SWORD, "サブウェポン", 2, function (Player $player) use ($taskScheduler) {
                SlotMenuSystem::send($player, new SelectSubGunTypeMenu($taskScheduler));
            }),
            new SlotMenuElement(ItemIds::IRON_SWORD, "試し打ち", 3, function (Player $player) use ($taskScheduler) {
                SlotMenuSystem::send($player, new SelectTrialGunTypesMenu($taskScheduler));
            }),
            new SlotMenuElement(ItemIds::IRON_SWORD, "銃購入", 4, function (Player $player) use ($taskScheduler) {
                SlotMenuSystem::send($player, new SelectGunTypeForSaleMenu($taskScheduler));
            })
        ];
        parent::__construct($menus);
    }
}