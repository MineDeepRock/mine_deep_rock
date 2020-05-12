<?php


namespace game_system\pmmp\form\trial_weapon_select_form;


use Closure;
use game_system\pmmp\form\weapon_purchase_form\GunPurchaseForm;
use gun_system\models\GunType;
use pocketmine\form\Form;
use pocketmine\Player;

class TrialWeaponSelectForm implements Form
{
    private $onSelected;


    public function __construct(Closure $onSelected) {
        $this->onSelected = $onSelected;
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        $buttons =
            [
                'Assault Rifle',
                'Handgun',
                'Revolver',
                'Shotgun',
                'Sub Machine Gun',
                'Light Machine Gun',
                'Sniper Rifle'
            ];
        switch ($buttons[$data]) {
            case 'Assault Rifle':
                $player->sendForm(new TrialGunSelectForm($this->onSelected, GunType::AssaultRifle()));
                break;
            case 'Handgun':
                $player->sendForm(new TrialGunSelectForm($this->onSelected, GunType::HandGun()));
                break;
            case 'Revolver':
                $player->sendForm(new TrialGunSelectForm($this->onSelected, GunType::Revolver()));
                break;
            case 'Shotgun':
                $player->sendForm(new TrialGunSelectForm($this->onSelected, GunType::Shotgun()));
                break;
            case 'Sub Machine Gun':
                $player->sendForm(new TrialGunSelectForm($this->onSelected, GunType::SMG()));
                break;
            case 'Light Machine Gun':
                $player->sendForm(new TrialGunSelectForm($this->onSelected, GunType::LMG()));
                break;
            case 'Sniper Rifle':
                $player->sendForm(new TrialGunSelectForm($this->onSelected, GunType::SniperRifle()));
                break;
        }
    }

    public function jsonSerialize() {
        return [
            'type' => 'form',
            'title' => '銃選択',
            'content' => '武器種',
            'buttons' => [
                ['text' => 'Assault Rifle'],
                ['text' => 'Handgun'],
                ['text' => 'Revolver'],
                ['text' => 'Shotgun'],
                ['text' => 'Sub Machine Gun'],
                ['text' => 'Light Machine Gun'],
                ['text' => 'Sniper Rifle']
            ]
        ];
    }
}