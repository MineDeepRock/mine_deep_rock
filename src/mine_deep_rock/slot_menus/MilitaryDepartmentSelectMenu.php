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

class MilitaryDepartmentSelectMenu extends SlotMenu
{
    public function __construct(array $menus) {
        $this->menus = [
            //Assault
            new SlotMenuElement(ItemIds::FIREBALL, "突撃兵", function (Player $player) {
                MilitaryDepartmentSystem::updateMilitaryDepartment($player->getName(), new AssaultSoldier());
            }),
            //Nursing
            new SlotMenuElement(ItemIds::SUGAR, "看護兵", function (Player $player) {
                MilitaryDepartmentSystem::updateMilitaryDepartment($player->getName(), new NursingSoldier());
            }),
            //Engineer
            new SlotMenuElement(ItemIds::SAND, "工兵", function (Player $player) {
                MilitaryDepartmentSystem::updateMilitaryDepartment($player->getName(), new Engineer());
            }),
            //Scout
            new SlotMenuElement(ItemIds::FIREBALL, "斥候兵", function (Player $player) {
                MilitaryDepartmentSystem::updateMilitaryDepartment($player->getName(), new Scout());
            }),
        ];
        parent::__construct($menus);
    }

}