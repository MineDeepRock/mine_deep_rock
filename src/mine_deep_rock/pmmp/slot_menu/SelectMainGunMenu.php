<?php


namespace mine_deep_rock\pmmp\slot_menu;


use gun_system\model\GunType;
use mine_deep_rock\dao\PlayerStatusDAO;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class SelectMainGunMenu extends SlotMenu
{
    public function __construct(Player $player) {
        $status = PlayerStatusDAO::get($player->getName());
        $sendGunSelectMenu = function (Player $player, GunType $gunType) {
            SlotMenuSystem::send($player,new SelectGunMenu($player->getName(),$gunType));
        };
        $menus = [
            //Back
            new SlotMenuElement(ItemIds::HOPPER, "戻る", 8, function (Player $player) {
                SlotMenuSystem::send($player, new SettingEquipmentsMenu());
            }),
        ];

        foreach ($status->getMilitaryDepartment()->getGunTypes() as $gunType) {
            $menus[] = new SlotMenuElement(ItemIds::BOW, $gunType->getTypeText(),0, function (Player $player) use ($sendGunSelectMenu,$gunType) {
                $sendGunSelectMenu($player, $gunType);
            });
        }

        parent::__construct($menus);
    }
}