<?php


namespace game_system\pmmp\form\weapon_select_form;


use Closure;
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

class WeaponSelectForm implements Form
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
                $weaponList = [];
                foreach ($this->ownWeapons as $weapon) {
                    if (GunList::fromString($weapon->getName()) instanceof AssaultRifle)
                        $weaponList[] = $weapon;
                }
                $player->sendForm(new GunSelectForm(
                    $this->onSelected,
                    GunType::AssaultRifle(),
                    $weaponList));
                break;
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
            case 'Shotgun':
                $weaponList = [];
                foreach ($this->ownWeapons as $weapon) {
                    if (GunList::fromString($weapon->getName()) instanceof Shotgun)
                        $weaponList[] = $weapon;
                }
                $player->sendForm(new GunSelectForm(
                    $this->onSelected,
                    GunType::Shotgun(),
                    $weaponList));
                break;
            case 'Sub Machine Gun':
                $weaponList = [];
                foreach ($this->ownWeapons as $weapon) {
                    if (GunList::fromString($weapon->getName()) instanceof SubMachineGun)
                        $weaponList[] = $weapon;
                }
                $player->sendForm(new GunSelectForm(
                    $this->onSelected,
                    GunType::SMG(),
                    $weaponList));
                break;
            case 'Light Machine Gun':
                $weaponList = [];
                foreach ($this->ownWeapons as $weapon) {
                    if (GunList::fromString($weapon->getName()) instanceof LightMachineGun)
                        $weaponList[] = $weapon;
                }
                $player->sendForm(new GunSelectForm(
                    $this->onSelected,
                    GunType::LMG(),
                    $weaponList));
                break;
            case 'Sniper Rifle':
                $weaponList = [];
                foreach ($this->ownWeapons as $weapon) {
                    if (GunList::fromString($weapon->getName()) instanceof SniperRifle)
                        $weaponList[] = $weapon;
                }
                $player->sendForm(new GunSelectForm(
                    $this->onSelected,
                    GunType::SniperRifle(),
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