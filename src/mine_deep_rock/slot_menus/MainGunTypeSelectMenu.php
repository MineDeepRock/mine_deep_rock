<?php


namespace mine_deep_rock\slot_menus;


use gun_system\models\GunType;
use military_department_system\MilitaryDepartmentSystem;
use military_department_system\models\AssaultSoldier;
use military_department_system\models\Engineer;
use military_department_system\models\NursingSoldier;
use military_department_system\models\Scout;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class MainGunTypeSelectMenu extends SlotMenu
{
    public function __construct(Player $player) {
        $militaryDepartment = MilitaryDepartmentSystem::getPlayerData($player->getName())->getMilitaryDepartment();
        $sendGunSelectMenu = function (Player $player, GunType $gunType) {
            SlotMenuSystem::send($player,new GunSelectMenu($player->getName(),$gunType));
        };
        $menus = [
            //Back
            new SlotMenuElement(ItemIds::HOPPER, "戻る", 8, function (Player $player) {
                SlotMenuSystem::send($player, new EquipmentSelectMenu());
            }),
        ];
        switch ($militaryDepartment::NAME) {
            case AssaultSoldier::NAME:
                array_push($menus,
                    new SlotMenuElement(ItemIds::BOW, GunType::AssaultRifle()->getTypeText(),0, function (Player $player) use ($sendGunSelectMenu) {
                        $sendGunSelectMenu($player, GunType::AssaultRifle());
                    }),
                    new SlotMenuElement(ItemIds::BOW, GunType::Shotgun()->getTypeText(),1, function (Player $player) use ($sendGunSelectMenu) {
                        $sendGunSelectMenu($player, GunType::Shotgun());
                    }));
                break;
            case NursingSoldier::NAME:
                array_push($menus,
                    new SlotMenuElement(ItemIds::BOW,  GunType::SMG()->getTypeText(),0, function (Player $player) use ($sendGunSelectMenu) {
                        $sendGunSelectMenu($player, GunType::SMG());
                    }));
                break;
            case Engineer::NAME:
                array_push($menus,
                    new SlotMenuElement(ItemIds::BOW, GunType::LMG()->getTypeText(),0, function (Player $player) use ($sendGunSelectMenu) {
                        $sendGunSelectMenu($player, GunType::LMG());
                    }));
                break;
            case Scout::NAME:
                array_push($menus,
                    new SlotMenuElement(ItemIds::BOW, GunType::SniperRifle()->getTypeText(),0, function (Player $player) use ($sendGunSelectMenu) {
                        $sendGunSelectMenu($player, GunType::SniperRifle());
                    }));
                break;
        }

        parent::__construct($menus);
    }
}