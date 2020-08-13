<?php

namespace mine_deep_rock\pmmp\form;


use form_builder\models\custom_form_elements\Dropdown;
use form_builder\models\custom_form_elements\Label;
use form_builder\models\CustomForm;
use gun_system\GunSystem;
use gun_system\model\Gun;
use gun_system\model\GunType;
use mine_deep_rock\dao\GunRecordDAO;
use mine_deep_rock\service\SelectMainGunService;
use mine_deep_rock\service\SelectSubGunService;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class GunDetailForm extends CustomForm
{
    /**
     * @var Dropdown
     */
    private $scopeMagnificationElement;
    private $gun;

    public function __construct(Player $player, Gun $gun) {
        $gunRecord = GunRecordDAO::get($player->getName(), $gun->getName());
        $this->scopeMagnificationElement = new Dropdown("スコープ", ["1", "2", "3"], $gunRecord->getScopeMagnification() - 1);
        $this->gun = $gun;
        parent::__construct($gun->getName() . ",kill数" . TextFormat::BOLD . $gunRecord->getKillCount(), [
            new Label(GunSystem::getGunDescription($gun)),
            $this->scopeMagnificationElement
        ]);
    }

    function onSubmit(Player $player): void {
        $scope = intval($this->scopeMagnificationElement->getResult());
        if ($this->gun->getType()->equals(GunType::HandGun()) || $this->gun->getType()->equals(GunType::Revolver())) {
            SelectSubGunService::execute($player->getName(), $this->gun->getName(), $scope);
        } else {
            SelectMainGunService::execute($player->getName(), $this->gun->getName(), $scope);
        }
    }

    function onClickCloseButton(Player $player): void { }
}