<?php


namespace game_system\pmmp\form\trial_weapon_select_form;


use Closure;
use game_system\pmmp\form\AttachmentSelectForm;
use gun_system\models\Gun;
use pocketmine\form\Form;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class TrialGunDetailForm implements Form
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

        $player->sendForm(new AttachmentSelectForm(function ($scopeName){
            ($this->onSelected)($this->gun::NAME,$scopeName);
        },$this->gun->getType()));
    }

    public function jsonSerialize() {
        return [
            'type' => 'form',
            'title' => '銃選択',
            'content' =>
                $this->gun::NAME . "\n" .
                TextFormat::RESET . "火力" . TextFormat::GRAY . $this->gun->getBulletDamage()->getValue() . "\n" .
                TextFormat::RESET . "レート" . TextFormat::GRAY . $this->gun->getRate()->getPerSecond() . "\n" .
                TextFormat::RESET . "リロード". TextFormat::GRAY . $this->gun->getReloadingType()->toString() . "\n" .
                TextFormat::RESET . "反動" . TextFormat::GRAY . $this->gun->getReaction() . "\n" .
                TextFormat::RESET . "精度" . TextFormat::GRAY . "ADS:" . $this->gun->getPrecision()->getADS() . "腰撃ち:" . $this->gun->getPrecision()->getHipShooting(),
            'buttons' => [
                [
                    'text' => '選択',
                    'image' => [
                        'type' => 'url',
                        'data' => 'textures/effective_ranges/' . $this->gun::NAME
                    ]
                ],
            ]
        ];
    }
}