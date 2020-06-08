<?php

namespace mine_deep_rock\pmmp\forms;


use gun_system\models\Gun;
use gun_system\models\GunType;
use military_department_system\MilitaryDepartmentSystem;
use pocketmine\form\Form;
use pocketmine\Player;
use weapon_data_system\models\GunData;
use weapon_data_system\WeaponDataSystem;

class GunDetailForm implements Form
{
    /**
     * @var Gun
     */
    private $gun;
    private $owner;
    private $scopes;

    public function __construct(Gun $gun, Player $owner) {
        $this->gun = $gun;
        $this->owner = $owner;
        $this->setCanEquipScopes();
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) return;


        $gunData = WeaponDataSystem::get($player->getName(), $this->gun::NAME);
        if ($gunData instanceof GunData) {
            $gunData = new GunData($gunData->getName(), $gunData->getKillCount(), $this->scopes[$data[1]]);
            WeaponDataSystem::update($player->getName(), $gunData);
        }
        if ($this->gun->getType()->equal(GunType::HandGun()) || $this->gun->getType()->equal(GunType::Revolver())) {
            MilitaryDepartmentSystem::updateEquipSubGun($this->owner->getName(), $this->gun::NAME);
        } else {
            MilitaryDepartmentSystem::updateEquipMainGun($this->owner->getName(), $this->gun::NAME);
        }
    }

    public function jsonSerialize() {
        return [
            'type' => 'custom_form',
            'title' => '銃選択',
            'content' => [
                [
                    'type' => 'label',
                    'text' => "kill数" . WeaponDataSystem::get($this->owner->getName(), $this->gun::NAME)->getKillCount() . $this->gun->getDescribe(),
                ],
                [
                    'type' => 'dropdown',
                    'text' => 'スコープ',
                    'options' => $this->scopes,
                    'default' => 0
                ],
            ]
        ];
    }

    private function setCanEquipScopes(): void {
        switch ($this->gun->getType()->getTypeText()) {
            case GunType::HandGun()->getTypeText():
                $this->scopes = [
                    'IronSight',
                    '2xScope',
                    '4xScope'];
                break;
            case GunType::AssaultRifle()->getTypeText():
                $this->scopes = [
                    'IronSight',
                    '2xScope',
                    '4xScope'];
                break;
            case GunType::Shotgun()->getTypeText():
                $this->scopes = ['IronSight'];
                break;
            case GunType::SMG()->getTypeText():
                $this->scopes = [
                    'IronSight',
                    '2xScope',
                    '4xScope'];
                break;
            case GunType::LMG()->getTypeText():
                $this->scopes = [
                    'IronSight',
                    '2xScope',
                    '4xScope'];
                break;
            case GunType::SniperRifle()->getTypeText():
                $this->scopes = [
                    'IronSight',
                    '2xScope',
                    '4xScope'];
                break;
            case GunType::Revolver()->getTypeText():
                $this->scopes = ['IronSight'];
                break;
        }
    }
}