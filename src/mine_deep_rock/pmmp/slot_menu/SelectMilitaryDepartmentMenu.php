<?php


namespace mine_deep_rock\pmmp\slot_menu;


use mine_deep_rock\model\MilitaryDepartment;
use mine_deep_rock\service\SelectMilitaryDepartmentService;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class SelectMilitaryDepartmentMenu extends SlotMenu
{
    public function __construct(SlotMenu $previousMenu) {
        $menus = [
            //Assault
            new SlotMenuElement(ItemIds::FIREBALL, "突撃兵", 0, function (Player $player) use ($previousMenu) {
                SelectMilitaryDepartmentService::execute($player->getName(), MilitaryDepartment::AssaultSoldier);
                SlotMenuSystem::send($player, $previousMenu);
            }),
            //Nursing
            new SlotMenuElement(ItemIds::SUGAR, "看護兵", 1, function (Player $player) use ($previousMenu) {
                SelectMilitaryDepartmentService::execute($player->getName(), MilitaryDepartment::NursingSoldier);
                SlotMenuSystem::send($player, $previousMenu);
            }),
            //Engineer
            new SlotMenuElement(ItemIds::SAND, "工兵", 2, function (Player $player) use ($previousMenu) {
                SelectMilitaryDepartmentService::execute($player->getName(), MilitaryDepartment::Engineer);
                SlotMenuSystem::send($player, $previousMenu);
            }),
            //Scout
            new SlotMenuElement(ItemIds::FIREBALL, "斥候兵", 3, function (Player $player) use ($previousMenu) {
                SelectMilitaryDepartmentService::execute($player->getName(), MilitaryDepartment::Scout);
                SlotMenuSystem::send($player, $previousMenu);
            }),
            //Back
            new SlotMenuElement(ItemIds::HOPPER, "戻る", 8, function (Player $player) use ($previousMenu) {
                SlotMenuSystem::send($player, $previousMenu);
            }),
        ];
        parent::__construct($menus);
    }
}