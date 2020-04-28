<?php


namespace game_system\pmmp\form;


use Closure;
use pocketmine\form\Form;
use pocketmine\Player;

class LMGSelectForm implements Form
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
                'LewisGun',
                'ParabellumMG14',
                'MG15',
                'BAR1918',
            ];
        ($this->onSelected)($buttons[$data]);
    }

    public function jsonSerialize() {
        return [
            'type' => 'form',
            'title' => '銃選択',
            'content' => 'Light Machine Gun',
            'buttons' => [
                ['text' => 'LewisGun'],
                ['text' => 'ParabellumMG14'],
                ['text' => 'MG15'],
                ['text' => 'BAR1918'],
            ]
        ];
    }
}