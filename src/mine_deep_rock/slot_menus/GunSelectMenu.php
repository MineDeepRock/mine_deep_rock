<?php


namespace mine_deep_rock\slot_menus;


use gun_system\models\GunList;
use gun_system\models\GunType;
use military_department_system\MilitaryDepartmentSystem;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;
use team_system\WeaponDataSystem;
use weapon_data_system\models\WeaponData;

class GunSelectMenu extends SlotMenu
{
    public function __construct(string $playerName, GunType $gunType) {
        $menus = [];

        foreach (WeaponDataSystem::getOwn($playerName) as $weaponData) {
            if ($weaponData instanceof WeaponData) {
                $gun = GunList::fromString($weaponData->getName());
                if ($gun !== null) {
                    if ($gun->getType()->equal($gunType)) {
                        $menus[] = new SlotMenuElement(ItemIds::BOW, $weaponData->getName(), function (Player $player) use ($weaponData) {
                            MilitaryDepartmentSystem::updateEquipMainGun($player->getName(), $weaponData->getName());
                        });
                    }
                }
            }
        }

        parent::__construct($menus);
    }

}