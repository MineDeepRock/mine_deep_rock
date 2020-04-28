<?php


namespace game_system\pmmp\form;


use Closure;
use pocketmine\form\Form;
use pocketmine\Player;

class SniperRifleSelectForm implements Form
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
                'SMLEMK3',
                'Gewehr98',
                'MartiniHenry',
                'Type38Arisaka',
            ];
        ($this->onSelected)($buttons[$data]);
    }

    public function jsonSerialize() {
        return [
            'type' => 'form',
            'title' => '銃選択',
            'content' => 'Sniper Rifle',
            'buttons' => [
                ['text' => 'SMLEMK3'],
                ['text' => 'Gewehr98'],
                ['text' => 'MartiniHenry'],
                ['text' => 'Type38Arisaka'],
            ]
        ];
    }
}