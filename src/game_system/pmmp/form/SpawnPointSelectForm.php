<?php


namespace game_system\pmmp\form;


use Closure;
use pocketmine\form\Form;
use pocketmine\Player;

class SpawnPointSelectForm implements Form
{
    private $onSelected;
    private $flags;

    public function __construct(Closure $onSelected, array $flags) {
        $this->onSelected = $onSelected;
        $this->flags = $flags;
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            ($this->onSelected)(null);
            return;
        }

        ($this->onSelected)($this->flags[$data]);
    }

    public function jsonSerialize() {
        $buttons = [];
        foreach ($this->flags as $flag) {
            $buttons[] = ["text" => $flag->getName()];
        }
        return [
            'type' => 'form',
            'title' => 'スポーン地点の選択',
            'content' => '拠点が占拠されていると初期リス地になります',
            'buttons' => $buttons
        ];
    }
}