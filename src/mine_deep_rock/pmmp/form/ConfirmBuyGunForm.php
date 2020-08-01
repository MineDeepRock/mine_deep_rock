<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\modal_form_elements\ModalFormButton;
use form_builder\models\ModalForm;
use mine_deep_rock\pmmp\slot_menu\SelectGunTypeForSaleMenu;
use mine_deep_rock\service\BuyGunService;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use slot_menu_system\SlotMenuSystem;

class ConfirmBuyGunForm extends ModalForm
{

    private $scheduler;
    private $gunName;

    public function __construct(string $gunName, TaskScheduler $taskScheduler) {
        $this->scheduler = $taskScheduler;
        $this->gunName = $gunName;
        parent::__construct($gunName,
            "{$gunName}を2000円で購入しますか",
            new ModalFormButton("はい"),
            new ModalFormButton("キャンセル")
        );
    }

    function onClickCloseButton(Player $player): void {
        SlotMenuSystem::send($player, new SelectGunTypeForSaleMenu($this->scheduler));
    }

    public function onClickButton1(Player $player): void {
        $result = BuyGunService::execute($player->getName(), $this->gunName);
        if ($result) {
            $player->sendMessage("{$this->gunName}を購入しました！");
        } else {
            $player->sendMessage("所持金が不足しているか、すでに所有しています");
        }

        SlotMenuSystem::send($player, new SelectGunTypeForSaleMenu($this->scheduler));
    }

    public function onClickButton2(Player $player): void {
        SlotMenuSystem::send($player, new SelectGunTypeForSaleMenu($this->scheduler));
    }
}