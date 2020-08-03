<?php


namespace mine_deep_rock\pmmp\slot_menu;


use gun_system\GunSystem;
use gun_system\model\Gun;
use gun_system\model\GunType;
use mine_deep_rock\dao\GunRecordDAO;
use mine_deep_rock\pmmp\form\ConfirmBuyGunForm;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;
use slot_menu_system\pmmp\items\SlotMenuElementItem;
use slot_menu_system\SlotMenuSystem;

class SelectGunTypeForSaleMenu extends SlotMenu
{
    public function __construct(TaskScheduler $taskScheduler) {
        $onSelected = function (Player $player, GunType $gunType) use ($taskScheduler) {
            $ownGunsName = [];
            foreach (GunRecordDAO::getOwn($player->getName()) as $gunRecord) {
                $ownGunsName[] = $gunRecord->getName();
            }

            $items = [];
            /** @var Gun $gun */
            foreach (GunSystem::loadAllGuns() as $gun) {
                if ($gun->getType()->equals($gunType) && !in_array($gun->getName(), $ownGunsName)) {
                    $item = new SlotMenuElementItem(new SlotMenuElement(ItemIds::BOW, $gun->getName(), 0, function (Player $player) use ($gun, $taskScheduler) {
                        $player->sendForm(new ConfirmBuyGunForm($gun, $taskScheduler));
                    }), ItemIds::BOW);

                    $item->setCustomName($gun->getName());
                    $items[] = $item;
                }
            }

            $player->getInventory()->setContents($items);
            $player->getInventory()->setItem(8, new SlotMenuElementItem(new SlotMenuElement(ItemIds::HOPPER, "戻る", 8, function (Player $player) use ($taskScheduler) {
                SlotMenuSystem::send($player, new SelectTrialGunTypesMenu($taskScheduler));
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
            new SlotMenuElement(ItemIds::HOPPER, "戻る", 8, function (Player $player) use ($taskScheduler) {
                SlotMenuSystem::send($player, new SettingEquipmentsMenu($taskScheduler));
            }),
        ];
        parent::__construct($menus);
    }
}