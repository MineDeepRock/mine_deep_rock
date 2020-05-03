<?php


namespace game_system\pmmp\form\weapon_purchase_form;


use Closure;
use gun_system\models\GunType;
use pocketmine\form\Form;
use pocketmine\Player;

class WeaponPurchaseForm implements Form
{
    private $onSelected;

    private $ownWeaponNames;

    public function __construct(Closure $onSelected, array $ownWeaponNames) {
        $this->onSelected = $onSelected;
        $this->ownWeaponNames = $ownWeaponNames;
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
                $player->sendForm(new GunPurchaseForm($this->onSelected, GunType::AssaultRifle(),$this->ownWeaponNames));
                break;
            case 'Handgun':
                $player->sendForm(new GunPurchaseForm($this->onSelected, GunType::HandGun(),$this->ownWeaponNames));
                break;
            case 'Revolver':
                $player->sendForm(new GunPurchaseForm($this->onSelected, GunType::Revolver(),$this->ownWeaponNames));
                break;
            case 'Shotgun':
                $player->sendForm(new GunPurchaseForm($this->onSelected, GunType::Shotgun(),$this->ownWeaponNames));
                break;
            case 'Sub Machine Gun':
                $player->sendForm(new GunPurchaseForm($this->onSelected, GunType::SMG(),$this->ownWeaponNames));
                break;
            case 'Light Machine Gun':
                $player->sendForm(new GunPurchaseForm($this->onSelected, GunType::LMG(),$this->ownWeaponNames));
                break;
            case 'Sniper Rifle':
                $player->sendForm(new GunPurchaseForm($this->onSelected, GunType::SniperRifle(),$this->ownWeaponNames));
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