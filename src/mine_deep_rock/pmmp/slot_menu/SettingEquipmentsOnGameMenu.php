<?php


namespace mine_deep_rock\pmmp\slot_menu;


use mine_deep_rock\pmmp\form\SelectSkillsForm;
use mine_deep_rock\pmmp\service\ResortPMMPService;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use slot_menu_system\models\SlotMenu;
use slot_menu_system\models\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class SettingEquipmentsOnGameMenu extends SlotMenu
{
    public function __construct(TaskScheduler $taskScheduler) {
        $menus = [
            new SlotMenuElement(ItemIds::COMPASS, "兵科", 0, function (Player $player) use ($taskScheduler) {
                SlotMenuSystem::send($player, new SelectMilitaryDepartmentMenu($this));
            }),
            new SlotMenuElement(ItemIds::WOODEN_SWORD, "専門技能", 1, function (Player $player) use ($taskScheduler) {
                $player->sendForm(new SelectSkillsForm($player));
            }),
            new SlotMenuElement(ItemIds::DIAMOND_SWORD, "メインウェポン", 2, function (Player $player) use ($taskScheduler) {
                SlotMenuSystem::send($player, new SelectMainGunTypeMenu($player, $this, $taskScheduler));
            }),
            new SlotMenuElement(ItemIds::IRON_SWORD, "サブウェポン", 3, function (Player $player) use ($taskScheduler) {
                SlotMenuSystem::send($player, new SelectSubGunTypeMenu($this, $taskScheduler));
            }),
            new SlotMenuElement(ItemIds::EMERALD, "再出撃", 8, function (Player $player) {
                ResortPMMPService::execute($player, null);
            }),
        ];
        parent::__construct($menus);
    }
}