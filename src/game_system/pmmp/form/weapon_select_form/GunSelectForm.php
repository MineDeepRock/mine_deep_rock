<?php


namespace game_system\pmmp\form\weapon_select_form;


use Closure;
use gun_system\models\GunType;
use pocketmine\form\Form;
use pocketmine\Player;

class GunSelectForm implements Form
{
    private $onSelected;

    private $gunType;
    private $gunNameList;

    public function __construct(Closure $onSelected, GunType $gunType, array $gunNameList) {
        $this->onSelected = $onSelected;
        $this->gunType = $gunType;
        $this->gunNameList = $gunNameList;
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        $player->sendForm(new GunSelectDetailForm($this->onSelected,$this->gunNameList[$data]));
    }

    public function jsonSerialize() {
        $buttons = [];
        foreach ($this->gunNameList as $name) {
            $buttons[] = ['text' => $name];
        }

        return [
            'type' => 'form',
            'title' => '銃選択',
            'content' => $this->gunType->getTypeText(),
            'buttons' => $buttons
        ];
    }
}