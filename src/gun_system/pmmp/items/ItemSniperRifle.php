<?php


namespace gun_system\pmmp\items;


use gun_system\models\sniper_rifle\SniperRifle;
use gun_system\pmmp\GunSounds;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;

class ItemSniperRifle extends ItemGun
{
    public function __construct(string $name, SniperRifle $gun, Player $owner) {
        $gun->setWhenBecomeReady(function(){
            $this->playCockingSound();
        });

        parent::__construct($name, $gun, $owner);
    }

    public function onReleaseUsing(Player $player): bool {
        $this->shootOnce();

        return true;
    }

    public function aim(): bool {
        return true;
    }

    private function playCockingSound(): void {
        $soundName = GunSounds::SniperRifleCocking()->getText();
        $packet = new PlaySoundPacket();
        $packet->x = $this->owner->x;
        $packet->y = $this->owner->y;
        $packet->z = $this->owner->z;
        $packet->volume = 6;
        $packet->pitch = 2;
        $packet->soundName = $soundName;
        $this->owner->sendDataPacket($packet);
    }
}