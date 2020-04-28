<?php


namespace game_system\pmmp\form;


use Closure;
use pocketmine\form\Form;
use pocketmine\Player;

class RevolverSelectForm implements Form
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
                'ColtSAA',
                'RevolverMk6',
                'No3Revolver',
                'NagantRevolver'
            ];
        ($this->onSelected)($buttons[$data]);
    }

    public function jsonSerialize() {
        return [
            'type' => 'form',
            'title' => '銃選択',
            'content' => 'Revolver',
            'buttons' => [
                ['text' => 'ColtSAA'],
                ['text' => 'RevolverMk6'],
                ['text' => 'No3Revolver'],
                ['text' => 'NagantRevolver']
            ]
        ];
    }
}