<?php


namespace game_system\pmmp\form\weapon_select_form;


use Closure;
use game_system\model\Weapon;
use gun_system\models\GunList;
use gun_system\models\GunType;
use pocketmine\form\Form;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class GunSelectDetailForm implements Form
{
    private $onSelected;

    private $weapon;
    private $gun;
    private $scopes;

    public function __construct(Closure $onSelected, Weapon $weapon) {
        $this->onSelected = $onSelected;
        $this->weapon = $weapon;
        $this->gun = GunList::fromString($weapon->getName());

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
        ($this->onSelected)($this->weapon->getName(), $scopeName);
    }

    public function jsonSerialize() {

        return [
            'type' => 'custom_form',
            'title' => '銃選択',
            'content' => [
                [
                    'type' => 'label',
                    'text' => "kill数" . $this->weapon->getKillCount()  . $this->gun->getDescribe(),
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
}