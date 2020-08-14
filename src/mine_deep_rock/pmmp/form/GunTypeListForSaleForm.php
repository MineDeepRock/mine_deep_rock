<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use gun_system\model\GunType;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class GunTypeListForSaleForm extends SimpleForm
{
    public function __construct(TaskScheduler $taskScheduler) {
        $buttons = [
            new SimpleFormButton(
                GunType::AssaultRifle()->getTypeText(),
                null,
                function (Player $player) use ($taskScheduler) {
                    $player->sendForm(new GunForSaleListForm($player, GunType::AssaultRifle(), $taskScheduler));
                }
            ),
            new SimpleFormButton(
                GunType::Shotgun()->getTypeText(),
                null,
                function (Player $player) use ($taskScheduler) {
                    $player->sendForm(new GunForSaleListForm($player, GunType::Shotgun(), $taskScheduler));
                }
            ),
            new SimpleFormButton(
                GunType::SMG()->getTypeText(),
                null,
                function (Player $player) use ($taskScheduler) {
                    $player->sendForm(new GunForSaleListForm($player, GunType::SMG(), $taskScheduler));
                }
            ),
            new SimpleFormButton(
                GunType::LMG()->getTypeText(),
                null,
                function (Player $player) use ($taskScheduler) {
                    $player->sendForm(new GunForSaleListForm($player, GunType::LMG(), $taskScheduler));
                }
            ),
            new SimpleFormButton(
                GunType::SniperRifle()->getTypeText(),
                null,
                function (Player $player) use ($taskScheduler) {
                    $player->sendForm(new GunForSaleListForm($player, GunType::SniperRifle(), $taskScheduler));
                }
            ),
            new SimpleFormButton(
                GunType::DMR()->getTypeText(),
                null,
                function (Player $player) use ($taskScheduler) {
                    $player->sendForm(new GunForSaleListForm($player, GunType::DMR(), $taskScheduler));
                }
            ),
            new SimpleFormButton(
                GunType::HandGun()->getTypeText(),
                null,
                function (Player $player) use ($taskScheduler) {
                    $player->sendForm(new GunForSaleListForm($player, GunType::HandGun(), $taskScheduler));
                }
            ),
            new SimpleFormButton(
                GunType::Revolver()->getTypeText(),
                null,
                function (Player $player) use ($taskScheduler) {
                    $player->sendForm(new GunForSaleListForm($player, GunType::Revolver(), $taskScheduler));
                }
            ),
        ];


        parent::__construct("銃の種類", "", $buttons);
    }

    function onClickCloseButton(Player $player): void { }
}