<?php


namespace mine_deep_rock\slot_menus;


use gun_system\models\GunList;
use gun_system\models\GunType;
use military_department_system\MilitaryDepartmentSystem;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;
use weapon_data_system\models\WeaponData;
use weapon_data_system\WeaponDataSystem;

class GunSelectMenu extends SlotMenu
{
    public function __construct(string $playerName, GunType $gunType) {
        $menus = [
            //Back
            new SlotMenuElement(ItemIds::HOPPER, "戻る", 8, function (Player $player) use ($gunType) {
                if ($gunType->equal(GunType::HandGun()) || $gunType->equal(GunType::Revolver())) {
                    SlotMenuSystem::send($player, new SubGunTypeSelectMenu());
                } else {
                    SlotMenuSystem::send($player, new MainGunTypeSelectMenu($player));
                }
            }),
        ];

        $index = 0;
        foreach (WeaponDataSystem::getOwn($playerName) as $weaponData) {
            if ($weaponData instanceof WeaponData) {
                $gun = GunList::fromString($weaponData->getName());
                if ($gun !== null) {
                    if ($gun->getType()->equal($gunType)) {
                        $menus[] = new SlotMenuElement(ItemIds::BOW, $weaponData->getName(), $index, function (Player $player) use ($weaponData) {
                            MilitaryDepartmentSystem::updateEquipMainGun($player->getName(), $weaponData->getName());
                        });
                        $index++;
                    }
                }
            }
        }

        parent::__construct($menus);
    }

}