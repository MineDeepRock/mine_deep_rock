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
                $gunNameList = [];
                foreach ($this->ownWeapons as $weapon) {
                    if (GunList::fromString($weapon->getName()) instanceof AssaultRifle)
                        $gunNameList[] = $weapon->getName();
                }
                $player->sendForm(new GunSelectForm(
                    $this->onSelected,
                    GunType::AssaultRifle(),
                    $gunNameList));
                break;
            case 'Handgun':
                $gunNameList = [];
                foreach ($this->ownWeapons as $weapon) {
                    if (GunList::fromString($weapon->getName()) instanceof Handgun)
                        $gunNameList[] = $weapon->getName();
                }
                $player->sendForm(new GunSelectForm(
                    $this->onSelected,
                    GunType::Handgun(),
                    $gunNameList));
                break;
            case 'Revolver':
                $gunNameList = [];
                foreach ($this->ownWeapons as $weapon) {
                    if (GunList::fromString($weapon->getName()) instanceof Revolver)
                        $gunNameList[] = $weapon->getName();
                }
                $player->sendForm(new GunSelectForm(
                    $this->onSelected,
                    GunType::Revolver(),
                    $gunNameList));
                break;
            case 'Shotgun':
                $gunNameList = [];
                foreach ($this->ownWeapons as $weapon) {
                    if (GunList::fromString($weapon->getName()) instanceof Shotgun)
                        $gunNameList[] = $weapon->getName();
                }
                $player->sendForm(new GunSelectForm(
                    $this->onSelected,
                    GunType::Shotgun(),
                    $gunNameList));
                break;
            case 'Sub Machine Gun':
                $gunNameList = [];
                foreach ($this->ownWeapons as $weapon) {
                    if (GunList::fromString($weapon->getName()) instanceof SubMachineGun)
                        $gunNameList[] = $weapon->getName();
                }
                $player->sendForm(new GunSelectForm(
                    $this->onSelected,
                    GunType::SMG(),
                    $gunNameList));
                break;
            case 'Light Machine Gun':
                $gunNameList = [];
                foreach ($this->ownWeapons as $weapon) {
                    if (GunList::fromString($weapon->getName()) instanceof LightMachineGun)
                        $gunNameList[] = $weapon->getName();
                }
                $player->sendForm(new GunSelectForm(
                    $this->onSelected,
                    GunType::LMG(),
                    $gunNameList));
                break;
            case 'Sniper Rifle':
                $gunNameList = [];
                foreach ($this->ownWeapons as $weapon) {
                    if (GunList::fromString($weapon->getName()) instanceof SniperRifle)
                        $gunNameList[] = $weapon->getName();
                }
                $player->sendForm(new GunSelectForm(
                    $this->onSelected,
                    GunType::SniperRifle(),
                    $gunNameList));
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