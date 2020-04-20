<?php


namespace gun_system\pmmp\items;


use gun_system\models\sniper_rifle\SniperRifle;
use pocketmine\Player;

class ItemSniperRifle extends ItemGun
{
    public function __construct(string $name, SniperRifle $gun, Player $owner) { parent::__construct($name, $gun, $owner); }

    public function onReleaseUsing(Player $player): bool {
        $this->tryShootingOnce();

        return true;
    }

    public function aim(): bool {
        if ($this->gun->isReloading()) {
            $this->owner->sendPopup("リロード中");
            return false;
        }

        if ($this->gun->getCurrentBullet() === 0) {
            $this->reload();
            return false;
        }
        return true;
    }
}