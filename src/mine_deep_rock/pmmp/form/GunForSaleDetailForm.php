<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\custom_form_elements\Label;
use form_builder\models\CustomForm;
use gun_system\GunSystem;
use gun_system\model\Gun;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class GunForSaleDetailForm extends CustomForm
{
    private $gun;
    private $scheduler;

    public function __construct(Gun $gun, TaskScheduler $taskScheduler) {
        $this->scheduler = $taskScheduler;
        $this->gun = $gun;
        parent::__construct($gun->getName(), [
            new Label(GunSystem::getGunDescription($gun)),
        ]);
    }

    function onSubmit(Player $player): void {
        $player->sendForm(new ConfirmBuyGunForm($this->gun, $this->scheduler));
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new GunForSaleListForm($player, $this->gun->getType(), $this->scheduler));
    }
}