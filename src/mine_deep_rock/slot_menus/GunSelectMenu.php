<?php


namespace mine_deep_rock\slot_menus;


use gun_system\models\GunList;
use gun_system\models\GunType;
use mine_deep_rock\pmmp\forms\GunDetailForm;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;
use weapon_data_system\models\GunData;
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
            if ($weaponData instanceof GunData) {
                $gun = GunList::fromString($weaponData->getName());
                if ($gun->getType()->equal($gunType)) {
                    $menus[] = new SlotMenuElement(ItemIds::BOW, $weaponData->getName(), $index, function (Player $player) use ($gun) {
                        $player->sendForm(new GunDetailForm($gun, $player));
                    });
                    $index++;
                }
            }
        }

        parent::__construct($menus);
    }

}