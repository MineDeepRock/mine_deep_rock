<?php


namespace game_system\pmmp\form;


use Closure;
use pocketmine\form\Form;
use pocketmine\Player;

class AssaultRifleSelectForm implements Form
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
                'M1907SL',
                'CeiRigotti',
                'FedorovAvtomat',
                'Ribeyrolles',
            ];
        ($this->onSelected)($buttons[$data]);
    }

    public function jsonSerialize() {
        return [
            'type' => 'form',
            'title' => '銃選択',
            'content' => 'Assault Rifle',
            'buttons' => [
                ['text' => 'M1907SL'],
                ['text' => 'CeiRigotti'],
                ['text' => 'FedorovAvtomat'],
                ['text' => 'Ribeyrolles'],
            ]
        ];
    }
}