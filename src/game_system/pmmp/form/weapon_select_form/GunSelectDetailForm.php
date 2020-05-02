<?php


namespace game_system\pmmp\form\weapon_select_form;


use Closure;
use gun_system\models\GunList;
use pocketmine\form\Form;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class GunSelectDetailForm implements Form
{
    private $onSelected;

    private $gunName;

    public function __construct(Closure $onSelected, string $gunName) {
        $this->onSelected = $onSelected;
        $this->gunName = $gunName;
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        ($this->onSelected)($this->gunName);
    }

    public function jsonSerialize() {
        $gun = GunList::fromString($this->gunName);

        return [
            'type' => 'form',
            'title' => '銃選択',
            'content' =>
                $this->gunName . "\n" .
                TextFormat::RESET . "火力" . TextFormat::GRAY . $gun->getBulletDamage()->getValue() . "\n" .
                TextFormat::RESET . "レート" . TextFormat::GRAY . $gun->getRate()->getPerSecond() . "\n" .
                TextFormat::RESET . "リロード". TextFormat::GRAY . $gun->getReloadingType()->toString() . "\n" .
                TextFormat::RESET . "反動" . TextFormat::GRAY . $gun->getReaction() . "\n" .
                TextFormat::RESET . "精度" . TextFormat::GRAY . "ADS:" . $gun->getPrecision()->getADS() . "腰撃ち:" . $gun->getPrecision()->getHipShooting(),
            'buttons' => [
                [
                    'text' => '選択',
                    'image' => [
                        'type' => 'url',
                        'data' => 'textures/effective_ranges/' . $this->gunName
                    ]
                ]
            ]
        ];
    }
}