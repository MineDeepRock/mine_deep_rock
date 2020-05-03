<?php


namespace game_system\pmmp\form\sub_weapon_select_form;


use Closure;
use game_system\pmmp\form\weapon_select_form\GunSelectForm;
use gun_system\models\assault_rifle\AssaultRifle;
use gun_system\models\GunList;
use gun_system\models\GunType;
use gun_system\models\hand_gun\HandGun;
use gun_system\models\light_machine_gun\LightMachineGun;
use gun_system\models\revolver\Revolver;
use gun_system\models\shotgun\Shotgun;
use gun_system\models\sniper_rifle\SniperRifle;
use gun_system\models\sub_machine_gun\SubMachineGun;
use pocketmine\form\Form;
use pocketmine\Player;

class SubWeaponSelectForm implements Form
{
    private $onSelected;

    private $ownWeapons;

    public function __construct(Closure $onSelected, array $ownWeapons) {
        $this->onSelected = $onSelected;
        $this->ownWeapons = $ownWeapons;
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        $buttons =
            [
                'Handgun',
                'Revolver'
            ];
        switch ($buttons[$data]) {
            case 'Handgun':
                $weaponList = [];
                foreach ($this->ownWeapons as $weapon) {
                    if (GunList::fromString($weapon->getName()) instanceof Handgun)
                        $weaponList[] = $weapon;
                }
                $player->sendForm(new GunSelectForm(
                    $this->onSelected,
                    GunType::Handgun(),
                    $weaponList));
                break;
            case 'Revolver':
                $weaponList = [];
                foreach ($this->ownWeapons as $weapon) {
                    if (GunList::fromString($weapon->getName()) instanceof Revolver)
                        $weaponList[] = $weapon;
                }
                $player->sendForm(new GunSelectForm(
                    $this->onSelected,
                    GunType::Revolver(),
                    $weaponList));
                break;
        }
    }

    public function jsonSerialize() {
        return [
            'type' => 'form',
            'title' => '銃選択',
            'content' => '武器種',
            'buttons' => [
                ['text' => 'Handgun'],
                ['text' => 'Revolver'],
            ]
        ];
    }
}