<?php


namespace mine_deep_rock\pmmp\slot_menu;


use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class SettingEquipmentsMenu extends  SlotMenu
{
    public function __construct() {
        $menus = [
            new SlotMenuElement(ItemIds::COMPASS, "兵科", 0, function (Player $player) {
                SlotMenuSystem::send($player, new SelectMilitaryDepartmentMenu());
            }),
            new SlotMenuElement(ItemIds::DIAMOND_SWORD, "メインウェポン", 1, function (Player $player) {
                SlotMenuSystem::send($player, new SelectMainGunMenu($player));
            }),
            new SlotMenuElement(ItemIds::IRON_SWORD, "サブウェポン", 2, function (Player $player) {
                SlotMenuSystem::send($player, new SelectSubGunMenu());
            }),
            new SlotMenuElement(ItemIds::IRON_SWORD, "試し打ち", 3, function (Player $player) {
                SlotMenuSystem::send($player, new SelectTrialGunTypesMenu());
            })
        ];
        parent::__construct($menus);
    }
}