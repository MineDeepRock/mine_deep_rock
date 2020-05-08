<?php


namespace game_system\pmmp\form\trial_weapon_select_form;


use Closure;
use gun_system\models\Gun;
use gun_system\models\GunType;
use pocketmine\form\Form;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class TrialGunDetailForm implements Form
{
    private $onSelected;

    private $gun;
    private $scopes;

    public function __construct(Closure $onSelected, Gun $gun) {
        $this->onSelected = $onSelected;
        $this->gun = $gun;

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

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        $scopeName = $this->scopes[$data[1]];
        ($this->onSelected)($this->gun::NAME, $scopeName);
    }

    public function jsonSerialize() {
        return [
            'type' => 'custom_form',
            'title' => '銃選択',
            'content' => [
                [
                    'type' => 'label',
                    'text' => $this->gun::NAME . "\n" .
                        TextFormat::RESET . "火力" . TextFormat::GRAY . $this->gun->getBulletDamage()->getValue() . "\n" .
                        TextFormat::RESET . "レート" . TextFormat::GRAY . $this->gun->getRate()->getPerSecond() . "\n" .
                        TextFormat::RESET . "リロード" . TextFormat::GRAY . $this->gun->getReloadingType()->toString() . "\n" .
                        TextFormat::RESET . "反動" . TextFormat::GRAY . $this->gun->getReaction() . "\n" .
                        TextFormat::RESET . "精度" . TextFormat::GRAY . "ADS:" . $this->gun->getPrecision()->getADS() . "腰撃ち:" . $this->gun->getPrecision()->getHipShooting()
                ],
                [
                    'type' => 'dropdown',
                    'text' => 'スコープ',
                    'options' => $this->scopes,
                    'default' => 0
                ],
            ],
        ];
    }
}