<?php


namespace game_system\pmmp\form;


use Closure;
use gun_system\models\GunType;
use pocketmine\form\Form;
use pocketmine\Player;

class AttachmentSelectForm implements Form
{
    private $onSelected;

    private $buttons;

    public function __construct(Closure $onSelected, GunType $gunType) {

        switch ($gunType->getTypeText()) {
            case GunType::HandGun()->getTypeText():
                $this->buttons = [
                    ['text' => 'IronSight'],
                    ['text' => '2xScope'],
                    ['text' => '4xScope'],
                ];
                break;
            case GunType::AssaultRifle()->getTypeText():
                $this->buttons = [
                    ['text' => 'IronSight'],
                    ['text' => '2xScope'],
                    ['text' => '4xScope'],
                ];
                break;
            case GunType::Shotgun()->getTypeText():
                $this->buttons = [
                    ['text' => 'IronSight'],
                ];
                break;
            case GunType::SMG()->getTypeText():
                $this->buttons = [
                    ['text' => 'IronSight'],
                    ['text' => '2xScope'],
                    ['text' => '4xScope'],
                ];
                break;
            case GunType::LMG()->getTypeText():
                $this->buttons = [
                    ['text' => 'IronSight'],
                    ['text' => '2xScope'],
                    ['text' => '4xScope'],
                ];
                break;
            case GunType::SniperRifle()->getTypeText():
                $this->buttons = [
                    ['text' => 'IronSight'],
                    ['text' => '2xScope'],
                    ['text' => '4xScope'],
                ];
                break;
            case GunType::Revolver()->getTypeText():
                $this->buttons = [
                    ['text' => 'IronSight'],
                ];
                break;
        }
        $this->onSelected = $onSelected;
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        $scopes =
            [
                'IronSight',
                '2xScope',
                '4xScope',
            ];

        ($this->onSelected)($scopes[$data]);
    }

    public function jsonSerialize() {
        return [
            'type' => 'form',
            'title' => 'スコープ選択',
            'content' => 'スコープ',
            'buttons' => $this->buttons,
        ];
    }
}