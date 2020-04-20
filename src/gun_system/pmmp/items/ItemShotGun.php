<?php


namespace gun_system\pmmp\items;


use gun_system\models\shotgun\Shotgun;
use gun_system\pmmp\entity\EntityBullet;
use gun_system\pmmp\GunSounds;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;

class ItemShotGun extends ItemGun
{
    public function __construct(string $name, Shotgun $gun, Player $owner) { parent::__construct($name, $gun, $owner); }

    protected function shootOnce(): void {
        $this->gun->tryShootingOnce(function ($scheduler) {
            $i = 0;
            while ($i < $this->gun->getPellets()) {
                EntityBullet::spawn($this->owner, $this->gun->getBulletSpeed()->getPerSecond(), $this->gun->getPrecision()->getValue(), $scheduler);
                $i++;
            }
            $this->doReaction();
            $this->owner->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getBulletCapacity());
            $this->playShootingSound();
        });
    }

    protected function shoot(): void {
        $this->gun->tryShooting(function ($scheduler) {
            $i = 0;
            while ($i < $this->gun->getPellets()) {
                EntityBullet::spawn($this->owner, $this->gun->getBulletSpeed()->getPerSecond(), $this->gun->getPrecision()->getValue(), $scheduler);
                $i++;
            }
            $this->doReaction();
            $this->owner->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getBulletCapacity());
            $this->playShootingSound();
            $this->playPumpActionSound();
        });
    }

    private function playPumpActionSound(): void {
        $soundName = GunSounds::ShotgunPumpAction()->getText();
        $packet = new PlaySoundPacket();
        $packet->x = $this->owner->x;
        $packet->y = $this->owner->y;
        $packet->z = $this->owner->z;
        $packet->volume = 3;
        $packet->pitch = 2;
        $packet->soundName = $soundName;
        $this->owner->sendDataPacket($packet);
    }
}