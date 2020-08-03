<?php


namespace mine_deep_rock\pmmp\slot_menu;


use gun_system\model\GunType;
use mine_deep_rock\dao\PlayerStatusDAO;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class SelectMainGunTypeMenu extends SlotMenu
{
    public function __construct(Player $player, SlotMenu $previousMenu, TaskScheduler $taskScheduler) {
        $status = PlayerStatusDAO::get($player->getName());
        $sendGunSelectMenu = function (Player $player, GunType $gunType) use ($taskScheduler) {
            SlotMenuSystem::send($player, new SelectMainGunMenu($player->getName(), $gunType, $this));
        };
        $menus = [
            //Back
            new SlotMenuElement(ItemIds::HOPPER, "戻る", 8, function (Player $player) use ($previousMenu) {
                SlotMenuSystem::send($player, $previousMenu);
            }),
        ];

        var_dump($status->getMilitaryDepartment()->getGunTypes());
        foreach ($status->getMilitaryDepartment()->getGunTypes() as $index => $gunType) {
            $menus[] = new SlotMenuElement(ItemIds::BOW, $gunType->getTypeText(), $index, function (Player $player) use ($sendGunSelectMenu, $gunType) {
                $sendGunSelectMenu($player, $gunType);
            });
        }

        parent::__construct($menus);
    }
}