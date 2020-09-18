<?php


namespace mine_deep_rock\pmmp\slot_menu;


use mine_deep_rock\pmmp\form\SelectSkillsForm;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use slot_menu_system\SlotMenu;
use slot_menu_system\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class SettingEquipmentsMenu extends SlotMenu
{
    public function __construct(TaskScheduler $taskScheduler) {
        $menus = [
            new SlotMenuElement(ItemIds::COMPASS, "兵科",  function (Player $player) use ($taskScheduler) {
                SlotMenuSystem::send($player, new SelectMilitaryDepartmentMenu($this));
            }),
            new SlotMenuElement(ItemIds::RECORD_MALL, "専門技能",  function (Player $player) use ($taskScheduler) {
                $player->sendForm(new SelectSkillsForm($player));
            }),
            new SlotMenuElement(ItemIds::DIAMOND_SWORD, "メインウェポン",  function (Player $player) use ($taskScheduler) {
                SlotMenuSystem::send($player, new SelectMainGunTypeMenu($player, $this, $taskScheduler));
            }),
            new SlotMenuElement(ItemIds::IRON_SWORD, "サブウェポン",  function (Player $player) use ($taskScheduler) {
                SlotMenuSystem::send($player, new SelectSubGunTypeMenu($this, $taskScheduler));
            }),
            new SlotMenuElement(ItemIds::WOODEN_SWORD, "試し打ち",  function (Player $player) use ($taskScheduler) {
                SlotMenuSystem::send($player, new SelectTrialGunTypesMenu($taskScheduler));
            }),
            //new SlotMenuElement(ItemIds::GOLDEN_SWORD, "銃購入", 4, function (Player $player) use ($taskScheduler) {
            //    SlotMenuSystem::send($player, new SelectGunTypeForSaleMenu($taskScheduler));
            //})
        ];
        parent::__construct($menus);
    }
}