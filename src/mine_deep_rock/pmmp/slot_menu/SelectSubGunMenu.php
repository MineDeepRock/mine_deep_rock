<?php


namespace mine_deep_rock\pmmp\slot_menu;


use gun_system\GunSystem;
use gun_system\model\GunType;
use mine_deep_rock\dao\GunRecordDAO;
use mine_deep_rock\pmmp\form\GunDetailForm;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use slot_menu_system\SlotMenu;
use slot_menu_system\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class SelectSubGunMenu extends SlotMenu
{
    public function __construct(string $playerName, GunType $gunType, SlotMenu $previousMenu, TaskScheduler $taskScheduler) {
        $menus = [
            //Back
            new SlotMenuElement(ItemIds::HOPPER, "戻る", function (Player $player) use ($gunType, $previousMenu) {
                SlotMenuSystem::send($player, $previousMenu);
            }, null, 8),
        ];

        foreach (GunRecordDAO::getOwn($playerName) as $gunRecord) {
            $gun = GunSystem::findGunByName($gunRecord->getName());

            if ($gun->getType()->equals($gunType)) {
                $menus[] = new SlotMenuElement(ItemIds::BOW, $gunRecord->getName(), function (Player $player) use ($gun) {
                    $player->sendForm(new GunDetailForm($player, $gun));
                });
            }
        }

        parent::__construct($menus, false);
    }
}