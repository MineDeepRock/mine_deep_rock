<?php

namespace mine_deep_rock\pmmp\slot_menu;


use gun_system\GunSystem;
use gun_system\model\Gun;
use gun_system\model\GunType;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use slot_menu_system\SlotMenu;
use slot_menu_system\SlotMenuElement;
use slot_menu_system\pmmp\items\SlotMenuElementItem;
use slot_menu_system\SlotMenuSystem;

class SelectTrialGunTypesMenu extends SlotMenu
{
    public function __construct(TaskScheduler $taskScheduler) {
        $onSelected = function (Player $player, GunType $gunType) use ($taskScheduler) {
            $items = [];
            /** @var Gun $gun */
            foreach (GunSystem::loadAllGuns() as $gun) {
                if ($gun->getType()->equals($gunType)) {
                    $items[] = GunSystem::getItemGun($gun->getName());
                }
            }

            $player->getInventory()->setContents($items);
            $player->getInventory()->setItem(8, new SlotMenuElementItem(new SlotMenuElement(ItemIds::HOPPER, "戻る", function (Player $player) use ($taskScheduler) {
                SlotMenuSystem::send($player, new SelectTrialGunTypesMenu($taskScheduler));
            }), ItemIds::HOPPER, 0));
            $player->getInventory()->setItem(7, ItemFactory::get(ItemIds::ARROW, 0, 1));
        };

        $menus = [
            new SlotMenuElement(ItemIds::BOW, GunType::AssaultRifle()->getTypeText(), function (Player $player) use ($onSelected) {
                $onSelected($player, GunType::AssaultRifle());
            }),
            new SlotMenuElement(ItemIds::BOW, GunType::Shotgun()->getTypeText(), function (Player $player) use ($onSelected) {
                $onSelected($player, GunType::Shotgun());
            }),
            new SlotMenuElement(ItemIds::BOW, GunType::SMG()->getTypeText(), function (Player $player) use ($onSelected) {
                $onSelected($player, GunType::SMG());
            }),
            new SlotMenuElement(ItemIds::BOW, GunType::LMG()->getTypeText(), function (Player $player) use ($onSelected) {
                $onSelected($player, GunType::LMG());
            }),
            new SlotMenuElement(ItemIds::BOW, GunType::SniperRifle()->getTypeText(), function (Player $player) use ($onSelected) {
                $onSelected($player, GunType::SniperRifle());
            }),
            new SlotMenuElement(ItemIds::BOW, GunType::DMR()->getTypeText(), function (Player $player) use ($onSelected) {
                $onSelected($player, GunType::DMR());
            }),
            new SlotMenuElement(ItemIds::BOW, GunType::HandGun()->getTypeText(), function (Player $player) use ($onSelected) {
                $onSelected($player, GunType::HandGun());
            }),
            new SlotMenuElement(ItemIds::BOW, GunType::Revolver()->getTypeText(), function (Player $player) use ($onSelected) {
                $onSelected($player, GunType::Revolver());
            }),
            new SlotMenuElement(ItemIds::HOPPER, "戻る", function (Player $player) use ($taskScheduler) {
                SlotMenuSystem::send($player, new SettingEquipmentsMenu($taskScheduler));
            }, null, 8),
        ];
        parent::__construct($menus);
    }
}