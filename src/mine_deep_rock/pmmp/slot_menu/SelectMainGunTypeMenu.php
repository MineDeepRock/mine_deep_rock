<?php


namespace mine_deep_rock\pmmp\slot_menu;


use gun_system\model\GunType;
use mine_deep_rock\dao\PlayerEquipmentsDAO;
use mine_deep_rock\dao\PlayerStatusDAO;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use slot_menu_system\SlotMenu;
use slot_menu_system\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class SelectMainGunTypeMenu extends SlotMenu
{
    public function __construct(Player $player, SlotMenu $previousMenu, TaskScheduler $taskScheduler) {
        $equipments = PlayerEquipmentsDAO::get($player->getName());


        $back = function (Player $player) use ($previousMenu) {
            SlotMenuSystem::send($player, $previousMenu);
        };
        $menus = [
            //Back
            new SlotMenuElement(ItemIds::HOPPER, "戻る", $back, $back, 8),
        ];

        $sendGunSelectMenu = function (Player $player, GunType $gunType) use ($taskScheduler) {
            SlotMenuSystem::send($player, new SelectMainGunMenu($player->getName(), $gunType, $this));
        };
        foreach ($equipments->getMilitaryDepartment()->getGunTypes() as $gunType) {
            $callBack = function (Player $player) use ($sendGunSelectMenu, $gunType) {
                $sendGunSelectMenu($player, $gunType);
            };
            $menus[] = new SlotMenuElement(ItemIds::BOW, $gunType->getTypeText(), $callBack, $callBack);
        }

        parent::__construct($menus, false);
    }
}