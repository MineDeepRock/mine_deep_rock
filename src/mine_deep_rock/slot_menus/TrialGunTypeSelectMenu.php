<?php


namespace mine_deep_rock\slot_menus;


use gun_system\GunSystem;
use gun_system\models\GunList;
use gun_system\models\GunType;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;
use slot_menu_system\pmmp\items\SlotMenuElementItem;
use slot_menu_system\SlotMenuSystem;

class TrialGunTypeSelectMenu extends SlotMenu
{
    public function __construct() {
        $onSelected = function (Player $player, GunType $gunType) {
            $items = [];
            if ($gunType->equal(GunType::AssaultRifle()))
                foreach (GunList::getAr() as $gun) $items[] = GunSystem::getGun($player, $gun::NAME, "IronSight");

            if ($gunType->equal(GunType::Shotgun()))
                foreach (GunList::getSg() as $gun) $items[] = GunSystem::getGun($player, $gun::NAME, "IronSight");

            if ($gunType->equal(GunType::SMG()))
                foreach (GunList::getSmg() as $gun) $items[] = GunSystem::getGun($player, $gun::NAME, "IronSight");

            if ($gunType->equal(GunType::LMG()))
                foreach (GunList::getLmg() as $gun) $items[] = GunSystem::getGun($player, $gun::NAME, "IronSight");

            if ($gunType->equal(GunType::SniperRifle()))
                foreach (GunList::getSn() as $gun) $items[] = GunSystem::getGun($player, $gun::NAME, "IronSight");

            if ($gunType->equal(GunType::HandGun()))
                foreach (GunList::getHg() as $gun) $items[] = GunSystem::getGun($player, $gun::NAME, "IronSight");

            if ($gunType->equal(GunType::Revolver()))
                foreach (GunList::getRv() as $gun) $items[] = GunSystem::getGun($player, $gun::NAME, "IronSight");

            $player->getInventory()->setContents($items);
            $player->getInventory()->setItem(8, new SlotMenuElementItem(new SlotMenuElement(ItemIds::HOPPER, "戻る", 8, function (Player $player) {
                SlotMenuSystem::send($player, new TrialGunTypeSelectMenu());
            }), ItemIds::HOPPER, 0));
            $player->getInventory()->setItem(7, ItemFactory::get(ItemIds::ARROW, 0, 1));
        };

        $menus = [
            new SlotMenuElement(ItemIds::BOW, GunType::AssaultRifle()->getTypeText(), 0, function (Player $player) use ($onSelected) {
                $onSelected($player, GunType::AssaultRifle());
            }),
            new SlotMenuElement(ItemIds::BOW, GunType::Shotgun()->getTypeText(), 1, function (Player $player) use ($onSelected) {
                $onSelected($player, GunType::Shotgun());
            }),
            new SlotMenuElement(ItemIds::BOW, GunType::SMG()->getTypeText(), 2, function (Player $player) use ($onSelected) {
                $onSelected($player, GunType::SMG());
            }),
            new SlotMenuElement(ItemIds::BOW, GunType::LMG()->getTypeText(), 3, function (Player $player) use ($onSelected) {
                $onSelected($player, GunType::LMG());
            }),
            new SlotMenuElement(ItemIds::BOW, GunType::SniperRifle()->getTypeText(), 4, function (Player $player) use ($onSelected) {
                $onSelected($player, GunType::SniperRifle());
            }),
            new SlotMenuElement(ItemIds::BOW, GunType::HandGun()->getTypeText(), 5, function (Player $player) use ($onSelected) {
                $onSelected($player, GunType::HandGun());
            }),
            new SlotMenuElement(ItemIds::BOW, GunType::Revolver()->getTypeText(), 6, function (Player $player) use ($onSelected) {
                $onSelected($player, GunType::Revolver());
            }),
            new SlotMenuElement(ItemIds::HOPPER, "戻る", 8, function (Player $player) {
                SlotMenuSystem::send($player, new EquipmentSelectMenu());
            }),
        ];
        parent::__construct($menus);
    }
}