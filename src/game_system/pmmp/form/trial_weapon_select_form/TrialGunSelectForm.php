<?php


namespace game_system\pmmp\form\trial_weapon_select_form;


use Closure;
use game_system\pmmp\form\weapon_select_form\GunSelectDetailForm;
use gun_system\models\GunList;
use gun_system\models\GunType;
use pocketmine\form\Form;
use pocketmine\Player;

class TrialGunSelectForm implements Form
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
                foreach ($gunListInstance->getHandguns() as $gun) {
                    $this->gunList[] = $gun;
                }
                break;

            case GunType::Revolver()->getTypeText():
                foreach ($gunListInstance->getRevolvers() as $gun) {
                    $this->gunList[] = $gun;
                }
                break;

            case GunType::AssaultRifle()->getTypeText():
                foreach ($gunListInstance->getAssaultRifles() as $gun) {
                    $this->gunList[] = $gun;
                }
                break;

            case GunType::Shotgun()->getTypeText():
                foreach ($gunListInstance->getShotguns() as $gun) {
                    $this->gunList[] = $gun;
                }
                break;

            case GunType::LMG()->getTypeText():
                foreach ($gunListInstance->getLMGs() as $gun) {
                    $this->gunList[] = $gun;
                }
                break;

            case GunType::SMG()->getTypeText():
                foreach ($gunListInstance->getSMGs() as $gun) {
                    $this->gunList[] = $gun;
                }
                break;

            case GunType::SniperRifle()->getTypeText():
                foreach ($gunListInstance->getSniperRifles() as $gun) {
                    $this->gunList[] = $gun;
                }
                break;
        }
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        $player->sendForm(new GunSelectDetailForm($this->onSelected, $this->gunList[$data]));
    }

    public function jsonSerialize() {
        $buttons = [];
        foreach ($this->gunList as $gun) {
            $buttons[] = ['text' => $gun::NAME];
        }
        return [
            'type' => 'form',
            'title' => '選択',
            'content' => $this->gunType->getTypeText(),
            'buttons' => $buttons
        ];
    }
}