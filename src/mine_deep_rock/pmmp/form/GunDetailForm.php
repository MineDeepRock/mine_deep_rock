<?php

namespace mine_deep_rock\pmmp\form;


use form_builder\models\custom_form_elements\Dropdown;
use form_builder\models\custom_form_elements\Label;
use form_builder\models\CustomForm;
use gun_system\GunSystem;
use gun_system\model\Gun;
use gun_system\model\GunType;
use mine_deep_rock\service\SelectMainGunService;
use mine_deep_rock\service\SelectSubGunService;
use pocketmine\Player;

class GunDetailForm extends CustomForm
{
    /**
     * @var Dropdown
     */
    private $scopeMagnificationElement;
    private $gun;

    public function __construct(Gun $gun) {
        $this->scopeMagnificationElement = new Dropdown("スコープ", [1, 2, 3]);
        $this->gun = $gun;
        parent::__construct($gun->getName(), [
            new Label(GunSystem::getGunDescription($gun)),
            $this->scopeMagnificationElement
        ]);
    }

    function onSubmit(Player $player): void {
        if ($this->gun->getType()->equals(GunType::HandGun()) || $this->gun->getType()->equals(GunType::Revolver())) {
            SelectSubGunService::execute($player->getName(), $this->gun->getName(), $this->scopeMagnificationElement->getResult());
        } else {
            SelectMainGunService::execute($player->getName(), $this->gun->getName(), $this->scopeMagnificationElement->getResult());
        }
    }

    function onClickCloseButton(Player $player): void { }
}