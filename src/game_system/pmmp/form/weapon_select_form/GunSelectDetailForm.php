<?php


namespace game_system\pmmp\form\weapon_select_form;


use Closure;
use game_system\model\Weapon;
use game_system\pmmp\form\AttachmentSelectForm;
use gun_system\models\GunList;
use pocketmine\form\Form;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class GunSelectDetailForm implements Form
{
    private $onSelected;

    private $weapon;

    public function __construct(Closure $onSelected, Weapon $weapon) {
        $this->onSelected = $onSelected;
        $this->weapon = $weapon;
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        $gun = GunList::fromString($this->weapon->getName());

        $player->sendForm(new AttachmentSelectForm(function ($scopeName){
            ($this->onSelected)($this->weapon->getName(),$scopeName);
        },$gun->getType()));
    }

    public function jsonSerialize() {
        $gun = GunList::fromString($this->weapon->getName());

        return [
            'type' => 'form',
            'title' => '銃選択',
            'content' =>
                $gun::NAME . "\n" .
                "kill数" . $this->weapon->getKillCount() . "\n" .
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
                        'data' => 'textures/effective_ranges/' . $gun::NAME
                    ]
                ],
            ]
        ];
    }
}