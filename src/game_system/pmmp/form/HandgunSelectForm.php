<?php


namespace game_system\pmmp\form;


use Closure;
use pocketmine\form\Form;
use pocketmine\Player;

class HandgunSelectForm implements Form
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
                'Mle1903',
                'P08',
                'C96',
                'HowdahPistol',
            ];
        ($this->onSelected)($buttons[$data]);
    }

    public function jsonSerialize() {
        return [
            'type' => 'form',
            'title' => '銃選択',
            'content' => 'Handgun',
            'buttons' => [
                ['text' => 'Mle1903'],
                ['text' => 'P08'],
                ['text' => 'C96'],
                ['text' => 'HowdahPistol'],
            ]
        ];
    }
}