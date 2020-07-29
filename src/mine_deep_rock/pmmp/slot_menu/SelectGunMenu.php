<?php


namespace mine_deep_rock\pmmp\slot_menu;


use gun_system\GunSystem;
use gun_system\model\GunType;
use mine_deep_rock\pmmp\form\GunDetailForm;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;
use weapon_data_system\models\GunData;
use weapon_data_system\WeaponDataSystem;

class SelectGunMenu extends SlotMenu
{
    public function __construct(string $playerName, GunType $gunType) {
        $menus = [
            //Back
            new SlotMenuElement(ItemIds::HOPPER, "戻る", 8, function (Player $player) use ($gunType) {
                if ($gunType->equals(GunType::HandGun()) || $gunType->equals(GunType::Revolver())) {
                    SlotMenuSystem::send($player, new SelectSubGunMenu());
                } else {
                    SlotMenuSystem::send($player, new SelectMainGunMenu($player));
                }
            }),
        ];

        $index = 0;
        foreach (WeaponDataSystem::getOwn($playerName) as $weaponData) {
            if ($weaponData instanceof GunData) {
                $gun = GunSystem::findGunByName($weaponData->getName());

                if ($gun->getType()->equals($gunType)) {
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