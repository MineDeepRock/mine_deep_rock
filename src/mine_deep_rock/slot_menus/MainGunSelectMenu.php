<?php


namespace mine_deep_rock\slot_menus;


use military_department_system\MilitaryDepartmentSystem;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;
use team_system\WeaponDataSystem;
use weapon_data_system\models\WeaponData;

class MainGunSelectMenu extends SlotMenu
{
    public function __construct(string $playerName) {
        $menus = array_map(function (WeaponData $weaponData) {
            return new SlotMenuElement(ItemIds::BOW, $weaponData->getName(), function (Player $player) use ($weaponData) {
                MilitaryDepartmentSystem::updateEquipMainGun($player->getName(), $weaponData->getName());
            });
        }, WeaponDataSystem::getOwn($playerName));

        parent::__construct($menus);
    }

}