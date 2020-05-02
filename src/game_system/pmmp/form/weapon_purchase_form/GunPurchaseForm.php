<?php


namespace game_system\pmmp\form\weapon_purchase_form;


use Closure;
use gun_system\models\GunList;
use gun_system\models\GunType;
use pocketmine\form\Form;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class GunPurchaseForm implements Form
{
    private $onSelected;

    private $gunType;
    private $gunList;

    public function __construct(Closure $onSelected, GunType $gunType) {
        $this->onSelected = $onSelected;
        $this->gunType = $gunType;
        $this->gunList = [];
        $gunListInstance = new GunList();

        switch ($gunType->getTypeText()) {
            case GunType::HandGun()->getTypeText():
                $this->gunList = $gunListInstance->getHandguns();
                break;

            case GunType::Revolver()->getTypeText():
                $this->gunList = $gunListInstance->getRevolvers();
                break;

            case GunType::AssaultRifle()->getTypeText():
                $this->gunList = $gunListInstance->getAssaultRifles();
                break;

            case GunType::Shotgun()->getTypeText():
                $this->gunList = $gunListInstance->getShotguns();
                break;

            case GunType::LMG()->getTypeText():
                $this->gunList = $gunListInstance->getLMGs();
                break;

            case GunType::SMG()->getTypeText():
                $this->gunList = $gunListInstance->getSMGs();
                break;

            case GunType::SniperRifle()->getTypeText():
                $this->gunList = $gunListInstance->getSniperRifles();
                break;
        }
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        $player->sendForm(new GunPurchaseDetailForm($this->onSelected,$this->gunList[$data]));
    }

    public function jsonSerialize() {
        $buttons = [];
        foreach ($this->gunList as $gun) {
            $buttons[] = ['text' => $gun::NAME];
        }
        return [
            'type' => 'form',
            'title' => '銃購入',
            'content' => $this->gunType->getTypeText(),
            'buttons' => $buttons
        ];
    }
}