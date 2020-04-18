<?php


namespace gun_system\pmmp\items;


use gun_system\models\sniper_rifle\SniperRifle;
use gun_system\pmmp\entity\EntityBullet;
use pocketmine\Player;

class ItemSniperRifle extends ItemGun
{
    public function __construct(string $name, SniperRifle $gun) { parent::__construct($name, $gun); }

    private $isAiming;

    public function onReleaseUsing(Player $player): bool {
        $this->isAiming = false;
        $this->shoot($player);

        return true;
    }

    public function aim(Player $player): bool {
        $this->isAiming = true;

        if ($this->gun->isReloading()) {
            $player->sendPopup("リロード中");
            return false;
        }

        if ($this->gun->getCurrentBullet() === 0) {
            $player->sendPopup("リロード");//TODO:ここじゃない
            $this->reload($player);

            return false;
        }

        return true;
    }

    public function shoot(Player $player): bool {
        $this->gun->shoot(function ($scheduler) use ($player) {
            EntityBullet::spawn($player, $this->gun->getBulletSpeed()->getPerSecond(), $this->gun->getPrecision()->getValue(), $this->gun->getRange(), $scheduler);
            $this->doReaction($player);
            $player->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getBulletCapacity());
            $this->playShootingSound($player);
        });

        return true;
    }
}