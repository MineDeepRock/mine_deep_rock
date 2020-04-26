<?php


namespace gun_system\pmmp\items;


use gun_system\models\attachment\bullet\ShotgunBulletType;
use gun_system\models\BulletId;
use gun_system\models\shotgun\attachment\scope\ShotgunScope;
use gun_system\models\shotgun\Shotgun;
use gun_system\pmmp\entity\EntityBullet;
use gun_system\pmmp\GunSounds;
use pocketmine\item\Item;
use pocketmine\Player;

class ItemShotGun extends ItemGun
{
    public function __construct(string $name, Shotgun $gun, Player $owner) {
        parent::__construct($name, $gun, $owner);

        $gun->setWhenBecomeReady(function () {
            $this->playPumpActionSound();
        });
    }

    public function setScope(ShotgunScope $scope): void {
        $this->gun->setScope($scope);
    }

    public function shootOnce(): void {
        $this->gun->cancelReloading();
        $result = $this->gun->tryShootingOnce(function ($scheduler) {
            $this->onSuccess($scheduler);
        });
        if (!$result->isSuccess())
            $this->owner->sendPopup($result->getMessage());
    }

    public function shoot(): void {
        $this->gun->cancelReloading();
        $result = $this->gun->tryShooting(function ($scheduler) {
            $this->onSuccess($scheduler);
        },$this->owner->isSneaking());
        if (!$result->isSuccess())
            $this->owner->sendPopup($result->getMessage());
    }

    private function onSuccess($scheduler): void {
        $bulletType = $this->gun->getBulletType();
        if ($bulletType->equal(ShotgunBulletType::Buckshot())) {
            $i = 0;
            while ($i < $this->gun->getPellets()) {
                EntityBullet::spawn($this->owner, $this->gun->getBulletSpeed()->getPerSecond(), $this->gun->getPrecision(), $scheduler);
                $i++;
            }
            $this->doReaction();
            $this->playShootingSound();
            $this->owner->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getMagazineCapacity());

        } else if ($bulletType->equal(ShotgunBulletType::Slug())) {
            EntityBullet::spawn($this->owner, $this->gun->getBulletSpeed()->getPerSecond(), $this->gun->getPrecision(), $scheduler);
            $this->doReaction();
            $this->playShootingSound();
            $this->owner->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getMagazineCapacity());

        }
    }

    public function reload() {
        $inventoryBullets = $this->getBulletAmount();

        $result = $this->gun->tryReload($this->owner,$inventoryBullets, function ($consumedBullets) {
            $this->owner->getInventory()->removeItem(Item::get(BulletId::fromGunType($this->gun->getType(), $this->gun->getBulletType()), 0, $consumedBullets));
            return $this->getBulletAmount();
        }, function () {
            $this->owner->sendPopup($this->gun->getCurrentBullet() . "/" . $this->gun->getMagazineCapacity());
        });

        if (!$result->isSuccess())
            $this->owner->sendPopup($result->getMessage());
    }

    private function playPumpActionSound(): void {
        $soundName = GunSounds::ShotgunPumpAction();
        GunSounds::play($this->owner,$soundName);
    }
}