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
    private $weaponList;

    public function __construct(Closure $onSelected, GunType $gunType, array $weaponList) {
        $this->onSelected = $onSelected;
        $this->gunType = $gunType;
        $this->weaponList = $weaponList;
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        $player->sendForm(new GunSelectDetailForm($this->onSelected, $this->weaponList[$data]));
    }

    public function jsonSerialize() {
        $buttons = [];
        foreach ($this->weaponList as $weapon) {
            $buttons[] = [
                'text' => $weapon->getName(),
                'image' => [
                    'type' => 'path',
                    'data' => 'textures/effective_ranges/' . $weapon->getName()
                ]
            ];
        }

        return [
            'type' => 'form',
            'title' => '銃選択',
            'content' => $this->gunType->getTypeText(),
            'buttons' => $buttons
        ];
    }
}