<?php


namespace mine_deep_rock\pmmp\slot_menu;


use gun_system\model\GunType;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use slot_menu_system\SlotMenu;
use slot_menu_system\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class SelectSubGunTypeMenu extends SlotMenu
{
    public function __construct(SlotMenu $previousMenu, TaskScheduler $taskScheduler) {
        $sendGunSelectMenu = function (Player $player, GunType $gunType) use ($taskScheduler) {
            SlotMenuSystem::send($player, new SelectSubGunMenu($player->getName(), $gunType, $this, $taskScheduler));
        };

        $menus = [
            new SlotMenuElement(ItemIds::BOW, GunType::HandGun()->getTypeText(), function (Player $player) use ($sendGunSelectMenu) {
                $sendGunSelectMenu($player, GunType::HandGun());
            }),
            new SlotMenuElement(ItemIds::BOW, GunType::Revolver()->getTypeText(), function (Player $player) use ($sendGunSelectMenu) {
                $sendGunSelectMenu($player, GunType::Revolver());
            }),
            //BACK
            new SlotMenuElement(ItemIds::HOPPER, "戻る", function (Player $player) use ($previousMenu) {
                SlotMenuSystem::send($player, $previousMenu);
            }, null, 8),
        ];
        parent::__construct($menus);
    }

}