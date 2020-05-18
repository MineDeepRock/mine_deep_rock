<?php


namespace game_system\pmmp\form\weapon_purchase_form;


use Closure;
use gun_system\models\Gun;
use pocketmine\form\Form;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class GunPurchaseDetailForm implements Form
{
    private $onSelected;

    private $gun;

    public function __construct(Closure $onSelected, Gun $gun) {
        $this->onSelected = $onSelected;
        $this->gun = $gun;
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        ($this->onSelected)($this->gun::NAME);
    }

    public function jsonSerialize() {
        return [
            'type' => 'form',
            'title' => '銃購入',
            'content' => $this->gun->getDescribe(),
            'buttons' => [
                [
                    'text' => '購入',
                ]
            ]
        ];
    }
}