<?php


namespace gun_system\pmmp\items;


use gun_system\models\sniper_rifle\attachment\scope\SniperRifleScope;
use gun_system\models\sniper_rifle\SniperRifle;
use gun_system\pmmp\GunSounds;
use pocketmine\Player;

class ItemSniperRifle extends ItemGun
{
    public function __construct(string $name, SniperRifle $gun, Player $owner) {
        $gun->setWhenBecomeReady(function () {
            $this->playCockingSound();
        });

        parent::__construct($name, $gun, $owner);
    }

    public function setScope(SniperRifleScope $scope): void {
        $this->gun->setScope($scope);
    }

    public function onReleaseUsing(Player $player): bool {
        $this->shootOnce();

        return true;
    }

    public function aim(): bool {
        return true;
    }

    private function playCockingSound(): void {
        $soundName = GunSounds::SniperRifleCocking();
        GunSounds::play($this->owner, $soundName);
    }
}