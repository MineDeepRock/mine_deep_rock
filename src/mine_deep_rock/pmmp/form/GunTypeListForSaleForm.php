<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\simple_form_elements\SimpleFormImage;
use form_builder\models\simple_form_elements\SimpleFormImageType;
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
                new SimpleFormImage(
                    SimpleFormImageType::Path(),
                    "textures/icon/" . GunType::AssaultRifle()->getTypeText()
                ),
                function (Player $player) use ($taskScheduler) {
                    $player->sendForm(new GunForSaleListForm($player, GunType::AssaultRifle(), $taskScheduler));
                }
            ),
            new SimpleFormButton(
                GunType::Shotgun()->getTypeText(),
                new SimpleFormImage(
                    SimpleFormImageType::Path(),
                    "textures/icon/" . GunType::Shotgun()->getTypeText()
                ),
                function (Player $player) use ($taskScheduler) {
                    $player->sendForm(new GunForSaleListForm($player, GunType::Shotgun(), $taskScheduler));
                }
            ),
            new SimpleFormButton(
                GunType::SMG()->getTypeText(),
                new SimpleFormImage(
                    SimpleFormImageType::Path(),
                    "textures/icon/" . GunType::SMG()->getTypeText()
                ),
                function (Player $player) use ($taskScheduler) {
                    $player->sendForm(new GunForSaleListForm($player, GunType::SMG(), $taskScheduler));
                }
            ),
            new SimpleFormButton(
                GunType::LMG()->getTypeText(),
                new SimpleFormImage(
                    SimpleFormImageType::Path(),
                    "textures/icon/" . GunType::LMG()->getTypeText()
                ),
                function (Player $player) use ($taskScheduler) {
                    $player->sendForm(new GunForSaleListForm($player, GunType::LMG(), $taskScheduler));
                }
            ),
            new SimpleFormButton(
                GunType::SniperRifle()->getTypeText(),
                new SimpleFormImage(
                    SimpleFormImageType::Path(),
                    "textures/icon/" . GunType::SniperRifle()->getTypeText()
                ),
                function (Player $player) use ($taskScheduler) {
                    $player->sendForm(new GunForSaleListForm($player, GunType::SniperRifle(), $taskScheduler));
                }
            ),
            new SimpleFormButton(
                GunType::DMR()->getTypeText(),
                new SimpleFormImage(
                    SimpleFormImageType::Path(),
                    "textures/icon/" . GunType::DMR()->getTypeText()
                ),
                function (Player $player) use ($taskScheduler) {
                    $player->sendForm(new GunForSaleListForm($player, GunType::DMR(), $taskScheduler));
                }
            ),
            new SimpleFormButton(
                GunType::HandGun()->getTypeText(),
                new SimpleFormImage(
                    SimpleFormImageType::Path(),
                    "textures/icon/" . GunType::HandGun()->getTypeText()
                ),
                function (Player $player) use ($taskScheduler) {
                    $player->sendForm(new GunForSaleListForm($player, GunType::HandGun(), $taskScheduler));
                }
            ),
            new SimpleFormButton(
                GunType::Revolver()->getTypeText(),
                new SimpleFormImage(
                    SimpleFormImageType::Path(),
                    "textures/icon/" . GunType::Revolver()->getTypeText()
                ),
                function (Player $player) use ($taskScheduler) {
                    $player->sendForm(new GunForSaleListForm($player, GunType::Revolver(), $taskScheduler));
                }
            ),
        ];


        parent::__construct("銃の種類", "", $buttons);
    }

    function onClickCloseButton(Player $player): void { }
}