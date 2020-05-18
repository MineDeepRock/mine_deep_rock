<?php


namespace game_system\pmmp\form;


use Closure;
use game_system\model\SpawnBeacon;
use pocketmine\form\Form;
use pocketmine\Player;

class SpawnPointSelectForm implements Form
{
    private $onSelected;
    private $flags;
    private $spawnBeacons;

    public function __construct(Closure $onSelected, array $flags, array $spawnBeacons) {
        $this->onSelected = $onSelected;
        $this->flags = $flags;
        $this->spawnBeacons = $spawnBeacons;
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            ($this->onSelected)(null);
            return;
        }

        $buttons = $this->flags + $this->spawnBeacons;

        ($this->onSelected)($buttons[$data]);
    }

    public function jsonSerialize() {
        $buttons = [];

        foreach ($this->flags as $flag) {
            $buttons[] = ["text" => $flag->getName()];
        }

        foreach ($this->spawnBeacons as $spawnBeacon) {
            $buttons[] = ["text" => $spawnBeacon->getDescribe()];
        }

        return [
            'type' => 'form',
            'title' => 'スポーン地点の選択',
            'content' => '拠点が占拠されていると初期リス地になります',
            'buttons' => $buttons
        ];
    }
}