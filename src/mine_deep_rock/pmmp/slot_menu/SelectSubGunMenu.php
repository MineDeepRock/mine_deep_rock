<?php


namespace mine_deep_rock\pmmp\slot_menu;


use gun_system\model\GunType;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class SelectSubGunMenu extends SlotMenu
{
    public function __construct() {
        $sendGunSelectMenu = function (Player $player, GunType $gunType) {
            SlotMenuSystem::send($player, new SelectGunMenu($player->getName(), $gunType));
        };

        $menus = [
            new SlotMenuElement(ItemIds::BOW, GunType::HandGun()->getTypeText(), 0, function (Player $player) use ($sendGunSelectMenu) {
                $sendGunSelectMenu($player, GunType::HandGun());
            }),
            new SlotMenuElement(ItemIds::BOW, GunType::Revolver()->getTypeText(), 1, function (Player $player) use ($sendGunSelectMenu) {
                $sendGunSelectMenu($player, GunType::Revolver());
            }),
            //BACK
            new SlotMenuElement(ItemIds::HOPPER, "戻る", 8, function (Player $player) {
                SlotMenuSystem::send($player, new SettingEquipmentsMenu());
            }),
        ];
        parent::__construct($menus);
    }

}