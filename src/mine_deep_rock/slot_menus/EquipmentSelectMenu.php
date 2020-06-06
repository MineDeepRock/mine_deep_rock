<?php


namespace mine_deep_rock\slot_menus;


use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class EquipmentSelectMenu extends SlotMenu
{
    public function __construct() {
        $menus = [
            new SlotMenuElement(ItemIds::COMPASS, "兵科", 0, function (Player $player) {
                SlotMenuSystem::send($player, new MilitaryDepartmentSelectMenu());
            }),
            new SlotMenuElement(ItemIds::DIAMOND_SWORD, "メインウェポン", 1, function (Player $player) {
                SlotMenuSystem::send($player, new MainGunTypeSelectMenu($player));
            }),
            new SlotMenuElement(ItemIds::IRON_SWORD, "サブウェポン", 2, function (Player $player) {
                SlotMenuSystem::send($player, new SubGunTypeSelectMenu());
            })
        ];
        parent::__construct($menus);
    }
}