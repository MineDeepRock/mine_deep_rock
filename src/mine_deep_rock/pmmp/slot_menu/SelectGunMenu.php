<?php


namespace mine_deep_rock\pmmp\slot_menu;


use gun_system\GunSystem;
use gun_system\model\GunType;
use mine_deep_rock\dao\GunRecordDAO;
use mine_deep_rock\pmmp\form\GunDetailForm;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class SelectGunMenu extends SlotMenu
{
    public function __construct(string $playerName, GunType $gunType, TaskScheduler $taskScheduler) {
        $menus = [
            //Back
            new SlotMenuElement(ItemIds::HOPPER, "戻る", 8, function (Player $player) use ($gunType, $taskScheduler) {
                if ($gunType->equals(GunType::HandGun()) || $gunType->equals(GunType::Revolver())) {
                    SlotMenuSystem::send($player, new SelectSubGunMenu($taskScheduler));
                } else {
                    SlotMenuSystem::send($player, new SelectMainGunMenu($player, $taskScheduler));
                }
            }),
        ];

        $index = 0;
        foreach (GunRecordDAO::getOwn($playerName) as $gunRecord) {
            $gun = GunSystem::findGunByName($gunRecord->getName());

            if ($gun->getType()->equals($gunType)) {
                $menus[] = new SlotMenuElement(ItemIds::BOW, $gunRecord->getName(), $index, function (Player $player) use ($gun) {
                    $player->sendForm(new GunDetailForm($gun));
                });
                $index++;
            }
        }

        parent::__construct($menus);
    }

}