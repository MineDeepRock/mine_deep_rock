<?php


namespace gun_system\pmmp\items;


use gun_system\models\BulletId;
use gun_system\models\shotgun\Shotgun;
use gun_system\pmmp\entity\EntityBullet;
use gun_system\pmmp\GunSounds;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;

class ItemShotGun extends ItemGun
{
    public function __construct(string $name, Shotgun $gun, Player $owner) {
        parent::__construct($name, $gun, $owner);

        $gun->setWhenBecomeReady(function () {
            $this->playPumpActionSound();
        });
    }


    public function shootOnce(): void {
        $this->gun->cancelReloading();
        $result = $this->gun->tryShootingOnce(function ($scheduler) {
            $i = 0;
            while ($i < $this->gun->getPellets()) {
                EntityBullet::spawn($this->owner, $this->gun->getBulletSpeed()->getPerSecond(), $this->gun->getPrecision()->getValue(), $scheduler);
                $i++;
            }
            $this->doReaction();
            $this->owner->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getBulletCapacity());
            $this->playShootingSound();
        });
        if (!$result->isSuccess())
            $this->owner->sendPopup($result->getMessage());
    }

    public function shoot(): void {
        $this->gun->cancelReloading();
        $result = $this->gun->tryShooting(function ($scheduler) {
            $i = 0;
            while ($i < $this->gun->getPellets()) {
                EntityBullet::spawn($this->owner, $this->gun->getBulletSpeed()->getPerSecond(), $this->gun->getPrecision()->getValue(), $scheduler);
                $i++;
            }
            $this->doReaction();
            $this->playShootingSound();
            $this->owner->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getBulletCapacity());
        });
        if (!$result->isSuccess())
            $this->owner->sendPopup($result->getMessage());
    }

    //TODO:これオーバーライドすんの微妙
    public function reload() {
        $inventoryBullets = $this->getBulletAmount();

        $this->playStartReloadingSound();
        $result = $this->gun->tryReload($inventoryBullets, function ($consumedBullets) {
            $this->owner->sendPopup("リロード");
            $this->owner->getInventory()->removeItem(Item::get(BulletId::fromGunType($this->gun->getType()), 0, $consumedBullets));
        }, function () {
            $this->owner->sendPopup($this->gun->getCurrentBullet() . "/" . $this->gun->getBulletCapacity());
            $this->playReloadSound();
        });

        if (!$result->isSuccess())
            $this->owner->sendPopup($result->getMessage());
    }

    private function playPumpActionSound(): void {
        $soundName = GunSounds::ShotgunPumpAction()->getText();
        $packet = new PlaySoundPacket();
        $packet->x = $this->owner->x;
        $packet->y = $this->owner->y;
        $packet->z = $this->owner->z;
        $packet->volume = 10;
        $packet->pitch = 2;
        $packet->soundName = $soundName;
        $this->owner->sendDataPacket($packet);
    }

    private function playReloadSound(): void {
        $soundName = GunSounds::ShotgunReload()->getText();
        $packet = new PlaySoundPacket();
        $packet->x = $this->owner->x;
        $packet->y = $this->owner->y;
        $packet->z = $this->owner->z;
        $packet->volume = 5;
        $packet->pitch = 2;
        $packet->soundName = $soundName;
        $this->owner->sendDataPacket($packet);
    }
}