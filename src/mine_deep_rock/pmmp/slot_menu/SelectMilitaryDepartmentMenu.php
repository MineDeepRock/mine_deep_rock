<?php


namespace mine_deep_rock\pmmp\slot_menu;


use mine_deep_rock\model\MilitaryDepartment;
use mine_deep_rock\service\SelectMilitaryDepartmentService;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\SlotMenu;
use slot_menu_system\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class SelectMilitaryDepartmentMenu extends SlotMenu
{
    public function __construct(SlotMenu $previousMenu) {

        $menus = [
            //Assault
            new SlotMenuElement(ItemIds::FIREBALL, "突撃兵", function (Player $player) use ($previousMenu) {
                SelectMilitaryDepartmentService::execute($player->getName(), MilitaryDepartment::AssaultSoldier);
                SlotMenuSystem::send($player, $previousMenu);
            }),
            //Nursing
            new SlotMenuElement(ItemIds::SUGAR, "看護兵", function (Player $player) use ($previousMenu) {
                SelectMilitaryDepartmentService::execute($player->getName(), MilitaryDepartment::NursingSoldier);
                SlotMenuSystem::send($player, $previousMenu);
            }),
            //Engineer
            new SlotMenuElement(ItemIds::SAND, "工兵", function (Player $player) use ($previousMenu) {
                SelectMilitaryDepartmentService::execute($player->getName(), MilitaryDepartment::Engineer);
                SlotMenuSystem::send($player, $previousMenu);
            }),
            //Scout
            new SlotMenuElement(ItemIds::FIREBALL, "斥候兵", function (Player $player) use ($previousMenu) {
                SelectMilitaryDepartmentService::execute($player->getName(), MilitaryDepartment::Scout);
                SlotMenuSystem::send($player, $previousMenu);
            }),
            //Back
            new SlotMenuElement(ItemIds::HOPPER, "戻る", function (Player $player) use ($previousMenu) {
                SlotMenuSystem::send($player, $previousMenu);
            }, null, 8),
        ];
        parent::__construct($menus, false);
    }
}