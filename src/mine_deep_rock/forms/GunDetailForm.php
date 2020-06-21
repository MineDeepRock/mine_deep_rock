<?php

namespace mine_deep_rock\forms;


use form_builder\models\custom_form_elements\Dropdown;
use form_builder\models\custom_form_elements\Label;
use form_builder\models\CustomForm;
use gun_system\models\Gun;
use gun_system\models\GunType;
use military_department_system\MilitaryDepartmentSystem;
use pocketmine\Player;
use weapon_data_system\models\GunData;
use weapon_data_system\WeaponDataSystem;


class GunDetailForm extends CustomForm
{
    /**
     * @var Gun
     */
    private $gun;
    private $scopes;

    private $scopeNameElement;

    public function __construct(Gun $gun, Player $player) {
        $this->gun = $gun;
        $this->initScopes();
        $this->scopeNameElement = new Dropdown("スコープ", $this->scopes, 0);
        parent::__construct($gun::NAME, [
            new Label(WeaponDataSystem::get($player->getName(), $this->gun::NAME)->getKillCount() . $this->gun->getDescribe()),
            $this->scopeNameElement
        ]);
    }

    function onSubmit(Player $player): void {
        $gunData = WeaponDataSystem::get($player->getName(), $this->gun::NAME);
        if ($gunData instanceof GunData) {
            $gunData = new GunData($gunData->getName(), $gunData->getKillCount(), $this->scopeNameElement->getResult());
            WeaponDataSystem::update($player->getName(), $gunData);
        }
        if ($this->gun->getType()->equal(GunType::HandGun()) || $this->gun->getType()->equal(GunType::Revolver())) {
            MilitaryDepartmentSystem::updateEquipSubGun($player->getName(), $this->gun::NAME);
        } else {
            MilitaryDepartmentSystem::updateEquipMainGun($player->getName(), $this->gun::NAME);
        }
    }

    function onClickCloseButton(Player $player): void {
        return;
    }

    private function initScopes(): void {
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