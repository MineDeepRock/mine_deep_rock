<?php


namespace game_system\pmmp\form;


use Closure;
use pocketmine\form\Form;
use pocketmine\Player;

class WeaponSelectForm implements Form
{
    private $onSelected;

    public function __construct(Closure $onSelected) {
        $this->onSelected = $onSelected;
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
                $player->sendForm(new AssaultRifleSelectForm($this->onSelected));
                break;
            case 'Handgun':
                $player->sendForm(new HandgunSelectForm($this->onSelected));
                break;
            case 'Revolver':
                $player->sendForm(new RevolverSelectForm($this->onSelected));
                break;
            case 'Shotgun':
                $player->sendForm(new ShotgunSelectForm($this->onSelected));
                break;
            case 'Sub Machine Gun':
                $player->sendForm(new SMGSelectForm($this->onSelected));
                break;
            case 'Light Machine Gun':
                $player->sendForm(new LMGSelectForm($this->onSelected));
                break;
            case 'Sniper Rifle':
                $player->sendForm(new SniperRifleSelectForm($this->onSelected));
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