<?php


namespace gun_system\pmmp\items;


use gun_system\models\BulletId;
use gun_system\models\sniper_rifle\attachment\scope\SniperRifleScope;
use gun_system\models\sniper_rifle\SniperRifle;
use gun_system\pmmp\GunSounds;
use pocketmine\item\Item;
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

    public function reload() {
        $inventoryBullets = $this->getBulletAmount();

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

    private function playReloadSound(): void {
        $soundName = GunSounds::SniperRifleReload()->getText();
        $packet = new PlaySoundPacket();
        $packet->x = $this->owner->x;
        $packet->y = $this->owner->y;
        $packet->z = $this->owner->z;
        $packet->volume = 5;
        $packet->pitch = 2;
        $packet->soundName = $soundName;
        $this->owner->sendDataPacket($packet);
    }

    private function playCockingSound(): void {
        $soundName = GunSounds::SniperRifleCocking()->getText();
        $packet = new PlaySoundPacket();
        $packet->x = $this->owner->x;
        $packet->y = $this->owner->y;
        $packet->z = $this->owner->z;
        $packet->volume = 20;
        $packet->pitch = 2;
        $packet->soundName = $soundName;
        $this->owner->sendDataPacket($packet);
    }
}