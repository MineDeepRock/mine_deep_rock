<?php


namespace game_system\pmmp\form;


use Closure;
use pocketmine\form\Form;
use pocketmine\Player;

class ShotgunSelectForm implements Form
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
                'M1897',
                'M1897 Slug',
                'Model10A',
                'Model10A Slug',
                'Automatic12G',
                'Automatic12G Slug',
                'Model1900',
                'Model1900 Slug',
            ];
        ($this->onSelected)($buttons[$data]);
    }

    public function jsonSerialize() {
        return [
            'type' => 'form',
            'title' => '銃選択',
            'content' => 'Shotgun',
            'buttons' => [
                ['text' => 'M1897'],
                ['text' => 'M1897 Slug'],
                ['text' => 'Model10A'],
                ['text' => 'Model10A Slug'],
                ['text' => 'Automatic12G'],
                ['text' => 'Automatic12G Slug'],
                ['text' => 'Model1900'],
                ['text' => 'Model1900 Slug'],
            ]
        ];
    }
}