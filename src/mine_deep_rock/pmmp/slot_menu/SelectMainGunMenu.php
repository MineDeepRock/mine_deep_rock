<?php


namespace mine_deep_rock\pmmp\slot_menu;


use gun_system\GunSystem;
use gun_system\model\GunType;
use mine_deep_rock\dao\GunRecordDAO;
use mine_deep_rock\pmmp\form\GunDetailForm;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\SlotMenu;
use slot_menu_system\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class SelectMainGunMenu extends SlotMenu
{
    public function __construct(string $playerName, GunType $gunType, SlotMenu $previousMenu) {
        $back = function (Player $player) use ($gunType, $previousMenu) {
            SlotMenuSystem::send($player, $previousMenu);
        };
        $menus = [
            new SlotMenuElement(ItemIds::HOPPER, "戻る", $back, $back, 8),
        ];

        foreach (GunRecordDAO::getOwn($playerName) as $gunRecord) {
            $gun = GunSystem::findGunByName($gunRecord->getName());

            if ($gun->getType()->equals($gunType)) {
                if ($gun->getName() === "MG0815") continue;

                $sendForm = function (Player $player) use ($gun) {
                    $player->sendForm(new GunDetailForm($player, $gun));
                };
                $menus[] = new SlotMenuElement(
                    ItemIds::BOW,
                    $gunRecord->getName(),
                    $sendForm,
                    $sendForm);
            }
        }

        parent::__construct($menus);
    }
}