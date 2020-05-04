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
    private $canEquipGunTypes;

    public function __construct(Closure $onSelected, array $ownWeapons, array $canEquipGunTypes) {
        $this->onSelected = $onSelected;
        $this->ownWeapons = $ownWeapons;
        $this->canEquipGunTypes = $canEquipGunTypes;
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        $buttons = array_map(function($gunType){
            return $gunType->getTypeText();
        },$this->canEquipGunTypes);

        switch ($buttons[$data]) {
            case GunType::AssaultRifle()->getTypeText():
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
            case GunType::Shotgun()->getTypeText():
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
            case GunType::SMG()->getTypeText():
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
            case GunType::LMG()->getTypeText():
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
            case GunType::SniperRifle()->getTypeText():
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
        $buttons = array_map(function($gunType){
            return ['text' => $gunType->getTypeText()];
        },$this->canEquipGunTypes);

        return [
            'type' => 'form',
            'title' => '銃選択',
            'content' => '武器種',
            'buttons' => $buttons
        ];
    }
}