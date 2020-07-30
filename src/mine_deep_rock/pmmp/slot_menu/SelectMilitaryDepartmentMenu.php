<?php


namespace mine_deep_rock\pmmp\slot_menu;


use mine_deep_rock\service\SelectMilitaryDepartmentService;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class SelectMilitaryDepartmentMenu extends SlotMenu
{
    public function __construct(TaskScheduler $taskScheduler) {
        $menus = [
            //Assault
            new SlotMenuElement(ItemIds::FIREBALL, "突撃兵", 0, function (Player $player) use ($taskScheduler) {
                SelectMilitaryDepartmentService::execute($player->getName(), "AssaultSoldier");
                SlotMenuSystem::send($player, new SettingEquipmentsMenu($taskScheduler));
            }),
            //Nursing
            new SlotMenuElement(ItemIds::SUGAR, "看護兵", 1, function (Player $player) use ($taskScheduler) {
                SelectMilitaryDepartmentService::execute($player->getName(), "NursingSoldier");
                SlotMenuSystem::send($player, new SettingEquipmentsMenu($taskScheduler));
            }),
            //Engineer
            new SlotMenuElement(ItemIds::SAND, "工兵", 2, function (Player $player) use ($taskScheduler) {
                SelectMilitaryDepartmentService::execute($player->getName(), "Engineer");
                SlotMenuSystem::send($player, new SettingEquipmentsMenu($taskScheduler));
            }),
            //Scout
            new SlotMenuElement(ItemIds::FIREBALL, "斥候兵", 3, function (Player $player) use ($taskScheduler) {
                SelectMilitaryDepartmentService::execute($player->getName(), "Scout");
                SlotMenuSystem::send($player, new SettingEquipmentsMenu($taskScheduler));
            }),
            //Back
            new SlotMenuElement(ItemIds::HOPPER, "戻る", 8, function (Player $player) use ($taskScheduler) {
                SlotMenuSystem::send($player, new SettingEquipmentsMenu($taskScheduler));
            }),
        ];
        parent::__construct($menus);
    }
}