<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\modal_form_elements\ModalFormButton;
use form_builder\models\ModalForm;
use gun_system\GunSystem;
use gun_system\model\Gun;
use mine_deep_rock\pmmp\slot_menu\SelectGunTypeForSaleMenu;
use mine_deep_rock\service\BuyGunService;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use slot_menu_system\SlotMenuSystem;

class ConfirmBuyGunForm extends ModalForm
{

    private $scheduler;
    private $gun;

    public function __construct(Gun $gun, TaskScheduler $taskScheduler) {
        $this->scheduler = $taskScheduler;
        $this->gun = $gun;
        parent::__construct($gun->getName(),
            "{$gun->getName()}を2000円で購入しますか\n" . GunSystem::getGunDescription($gun),
            new ModalFormButton("はい"),
            new ModalFormButton("キャンセル")
        );
    }

    function onClickCloseButton(Player $player): void {
        SlotMenuSystem::send($player, new SelectGunTypeForSaleMenu($this->scheduler));
    }

    public function onClickButton1(Player $player): void {
        $result = BuyGunService::execute($player->getName(), $this->gun->getName());
        if ($result) {
            $player->sendMessage("{$this->gun->getName()}を購入しました！");
        } else {
            $player->sendMessage("所持金が不足しているか、すでに所有しています");
        }

        SlotMenuSystem::send($player, new SelectGunTypeForSaleMenu($this->scheduler));
    }

    public function onClickButton2(Player $player): void {
        SlotMenuSystem::send($player, new SelectGunTypeForSaleMenu($this->scheduler));
    }
}