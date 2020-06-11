<?php


namespace mine_deep_rock\slot_menus;


use military_department_system\MilitaryDepartmentSystem;
use military_department_system\models\AssaultSoldier;
use military_department_system\models\Engineer;
use military_department_system\models\NursingSoldier;
use military_department_system\models\Scout;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class MilitaryDepartmentSelectMenu extends SlotMenu
{
    public function __construct() {
        $menus = [
            //Assault
            new SlotMenuElement(ItemIds::FIREBALL, "突撃兵", 0, function (Player $player) {
                MilitaryDepartmentSystem::updateMilitaryDepartment($player->getName(), new AssaultSoldier());
                SlotMenuSystem::send($player, new EquipmentSelectMenu());
            }),
            //Nursing
            new SlotMenuElement(ItemIds::SUGAR, "看護兵", 1, function (Player $player) {
                MilitaryDepartmentSystem::updateMilitaryDepartment($player->getName(), new NursingSoldier());
                SlotMenuSystem::send($player, new EquipmentSelectMenu());
            }),
            //Engineer
            new SlotMenuElement(ItemIds::SAND, "工兵", 2, function (Player $player) {
                MilitaryDepartmentSystem::updateMilitaryDepartment($player->getName(), new Engineer());
                SlotMenuSystem::send($player, new EquipmentSelectMenu());
            }),
            //Scout
            new SlotMenuElement(ItemIds::FIREBALL, "斥候兵", 3, function (Player $player) {
                MilitaryDepartmentSystem::updateMilitaryDepartment($player->getName(), new Scout());
                SlotMenuSystem::send($player, new EquipmentSelectMenu());
            }),
            //Back
            new SlotMenuElement(ItemIds::HOPPER, "戻る", 8, function (Player $player) {
                SlotMenuSystem::send($player, new EquipmentSelectMenu());
            }),
        ];
        parent::__construct($menus);
    }

}