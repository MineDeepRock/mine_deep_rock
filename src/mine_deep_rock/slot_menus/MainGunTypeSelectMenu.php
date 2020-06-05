<?php


namespace mine_deep_rock\slot_menus;


use gun_system\models\GunType;
use military_department_system\MilitaryDepartmentSystem;
use military_department_system\models\AssaultSoldier;
use military_department_system\models\Engineer;
use military_department_system\models\MilitaryDepartment;
use military_department_system\models\NursingSoldier;
use military_department_system\models\Scout;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;

class MainGunTypeSelectMenu extends SlotMenu
{
    public function __construct(MilitaryDepartment $militaryDepartment) {
        $sendGunSelectMenu = function (Player $player, GunType $gunType) {
            //TODO:実装
        };
        $menus = [];
        switch ($militaryDepartment::NAME) {
            case AssaultSoldier::NAME:
                $menus = [
                    new SlotMenuElement(ItemIds::BOW, "", function (Player $player) use ($sendGunSelectMenu) {
                        $sendGunSelectMenu($player, GunType::AssaultRifle());
                    }),
                    new SlotMenuElement(ItemIds::BOW, "", function (Player $player) use ($sendGunSelectMenu) {
                        $sendGunSelectMenu($player, GunType::Shotgun());
                    }),
                ];
                break;
            case NursingSoldier::NAME:
                $menus = [
                    new SlotMenuElement(ItemIds::BOW, "", function (Player $player) use ($sendGunSelectMenu) {
                        $sendGunSelectMenu($player, GunType::SMG());
                    }),
                ];
                break;
            case Engineer::NAME:
                $menus = [
                    new SlotMenuElement(ItemIds::BOW, "", function (Player $player) use ($sendGunSelectMenu) {
                        $sendGunSelectMenu($player, GunType::LMG());
                    }),
                ];
                break;
            case Scout::NAME:
                $menus = [
                    new SlotMenuElement(ItemIds::BOW, "", function (Player $player) use ($sendGunSelectMenu) {
                        $sendGunSelectMenu($player, GunType::SniperRifle());
                    }),
                ];
                break;
        }

        parent::__construct($menus);
    }
}