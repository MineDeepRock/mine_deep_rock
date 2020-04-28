<?php


namespace game_system\pmmp\form;


use Closure;
use pocketmine\form\Form;
use pocketmine\Player;

class SMGSelectForm implements Form
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
                'MP18',
                'Automatico',
                'Hellriegel1915',
                'FrommerStopAuto',
            ];
        ($this->onSelected)($buttons[$data]);
    }

    public function jsonSerialize() {
        return [
            'type' => 'form',
            'title' => '銃選択',
            'content' => 'Sub Machine Gun',
            'buttons' => [
                ['text' => 'MP18'],
                ['text' => 'Automatico'],
                ['text' => 'Hellriegel1915'],
                ['text' => 'FrommerStopAuto'],
            ]
        ];
    }
}