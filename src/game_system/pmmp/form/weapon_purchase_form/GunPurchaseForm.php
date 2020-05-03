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

    public function __construct(Closure $onSelected, GunType $gunType, array $ownWeaponNames) {
        $this->onSelected = $onSelected;
        $this->gunType = $gunType;
        $this->gunList = [];
        $gunListInstance = new GunList();

        switch ($gunType->getTypeText()) {
            case GunType::HandGun()->getTypeText():
                foreach ($gunListInstance->getHandguns() as $gun) {
                    if (!in_array($gun::NAME,$ownWeaponNames)) {
                        $this->gunList[] = $gun;
                    }
                }
                break;

            case GunType::Revolver()->getTypeText():
                foreach ($gunListInstance->getRevolvers() as $gun) {
                    if (!in_array($gun::NAME,$ownWeaponNames)) {
                        $this->gunList[] = $gun;
                    }
                }
                break;

            case GunType::AssaultRifle()->getTypeText():
                foreach ($gunListInstance->getAssaultRifles() as $gun) {
                    if (!in_array($gun::NAME,$ownWeaponNames)) {
                        $this->gunList[] = $gun;
                    }
                }
                break;

            case GunType::Shotgun()->getTypeText():
                foreach ($gunListInstance->getShotguns() as $gun) {
                    if (!in_array($gun::NAME,$ownWeaponNames)) {
                        $this->gunList[] = $gun;
                    }
                }
                break;

            case GunType::LMG()->getTypeText():
                foreach ($gunListInstance->getLMGs() as $gun) {
                    if (!in_array($gun::NAME,$ownWeaponNames)) {
                        $this->gunList[] = $gun;
                    }
                }
                break;

            case GunType::SMG()->getTypeText():
                foreach ($gunListInstance->getSMGs() as $gun) {
                    if (!in_array($gun::NAME,$ownWeaponNames)) {
                        $this->gunList[] = $gun;
                    }
                }
                break;

            case GunType::SniperRifle()->getTypeText():
                foreach ($gunListInstance->getSniperRifles() as $gun) {
                    if (!in_array($gun::NAME,$ownWeaponNames)) {
                        $this->gunList[] = $gun;
                    }
                }
                break;
        }
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        $player->sendForm(new GunPurchaseDetailForm($this->onSelected, $this->gunList[$data]));
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