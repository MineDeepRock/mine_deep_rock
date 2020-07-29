<?php

namespace mine_deep_rock\pmmp\form;


use form_builder\models\CustomForm;
use gun_system\model\Gun;
use pocketmine\Player;

class GunDetailForm extends CustomForm
{
    public function __construct(Gun $gun,Player $player) {
        parent::__construct($gun->getName(), [

        ]);
    }

    function onSubmit(Player $player): void {
        // TODO: Implement onSubmit() method.
    }

    function onClickCloseButton(Player $player): void {
        // TODO: Implement onClickCloseButton() method.
    }
}