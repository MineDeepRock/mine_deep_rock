<?php


namespace game_system\pmmp\form\weapon_purchase_form;


use Closure;
use gun_system\models\GunList;
use gun_system\models\GunType;
use pocketmine\form\Form;
use pocketmine\Player;

class GunPurchaseForm implements Form
{
    private $onSelected;

    private $gunType;
    private $gunNameList;

    public function __construct(Closure $onSelected, GunType $gunType) {
        $this->onSelected = $onSelected;
        $this->gunType = $gunType;
        $this->gunNameList = [];
        $gunListInstance = new GunList();

        switch ($gunType->getTypeText()) {
            case GunType::HandGun()->getTypeText():
                $this->gunNameList = array_map(function ($gun) {
                    return $gun::NAME;
                }, $gunListInstance->getHandguns());
                break;

            case GunType::AssaultRifle()->getTypeText():
                $this->gunNameList = array_map(function ($gun) {
                    return $gun::NAME;
                }, $gunListInstance->getAssaultRifles());
                break;

            case GunType::LMG()->getTypeText():
                $this->gunNameList = array_map(function ($gun) {
                    return $gun::NAME;
                }, $gunListInstance->getLMGs());
                break;

            case GunType::SniperRifle()->getTypeText():
                $this->gunNameList = array_map(function ($gun) {
                    return $gun::NAME;
                }, $gunListInstance->getSniperRifles());
                break;

            case GunType::SMG()->getTypeText():
                $this->gunNameList = array_map(function ($gun) {
                    return $gun::NAME;
                }, $gunListInstance->getSMGs());
                break;

            case GunType::Revolver()->getTypeText():
                $this->gunNameList = array_map(function ($gun) {
                    return $gun::NAME;
                }, $gunListInstance->getRevolvers());
                break;
        }
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        ($this->onSelected)($this->gunNameList[$data]);
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