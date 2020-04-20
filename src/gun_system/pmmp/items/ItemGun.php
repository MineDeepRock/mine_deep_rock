<?php


namespace gun_system\pmmp\items;


use gun_system\models\BulletId;
use gun_system\models\Gun;
use gun_system\pmmp\entity\EntityBullet;
use gun_system\pmmp\GunSounds;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\Tool;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;

abstract class ItemGun extends Tool
{
    protected $owner;

    protected $gun;

    public function __construct(string $name, Gun $gun, Player $owner) {
        $this->owner = $owner;
        $this->gun = $gun;
        parent::__construct(ItemIds::BOW, 0, $name);
    }

    public function getMaxDurability(): int {
        return 100;
    }

    public function playShootingSound(): void {
        $soundName = GunSounds::shootSoundFromGunType($this->gun->getType())->getText();
        $packet = new PlaySoundPacket();
        $packet->x = $this->owner->x;
        $packet->y = $this->owner->y;
        $packet->z = $this->owner->z;
        $packet->volume = 3;
        $packet->pitch = 2;
        $packet->soundName = $soundName;
        $this->owner->sendDataPacket($packet);
    }

    public function playStartReloadingSound(): void {
        $soundName = GunSounds::startReloadingSoundFromGunType($this->gun->getType())->getText();
        $packet = new PlaySoundPacket();
        $packet->x = $this->owner->x;
        $packet->y = $this->owner->y;
        $packet->z = $this->owner->z;
        $packet->volume = 3;
        $packet->pitch = 2;
        $packet->soundName = $soundName;
        $this->owner->sendDataPacket($packet);
    }

    public function playEndReloadingSound(): void {
        $soundName = GunSounds::endReloadingSoundFromGunType($this->gun->getType())->getText();
        $packet = new PlaySoundPacket();
        $packet->x = $this->owner->x;
        $packet->y = $this->owner->y;
        $packet->z = $this->owner->z;
        $packet->volume = 3;
        $packet->pitch = 2;
        $packet->soundName = $soundName;
        $this->owner->sendDataPacket($packet);
    }


    public function onReleaseUsing(Player $player): bool {
        $this->gun->cancelShooting();
        return true;
    }

    public function tryShootingOnce() {
        if ($this->gun->isReloading()) {
            $this->owner->sendPopup("リロード中");
            return false;
        }

        if ($this->gun->getCurrentBullet() === 0) {
            $this->owner->sendPopup("リロード");//TODO:ここじゃない
            $this->reload();
            return false;
        }

        $this->shootOnce();

        return true;
    }

    public function tryShooting(): bool {
        if ($this->gun->isReloading()) {
            $this->owner->sendPopup("リロード中");
            return false;
        }

        if ($this->gun->getCurrentBullet() === 0) {
            $this->reload();
            return false;
        }

        $this->shoot();

        return true;
    }

    protected function shoot(): void {
        $this->gun->tryShooting(function ($scheduler) {
            $this->playShootingSound();
            EntityBullet::spawn($this->owner, $this->gun->getBulletSpeed()->getPerSecond(), $this->gun->getPrecision()->getValue(), $scheduler);
            $this->doReaction();
            $this->owner->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getBulletCapacity());
        });
    }

    protected function shootOnce(): void {
        $this->gun->tryShootingOnce(function ($scheduler)  {
            $this->playShootingSound();
            EntityBullet::spawn($this->owner, $this->gun->getBulletSpeed()->getPerSecond(), $this->gun->getPrecision()->getValue(), $scheduler);
            $this->doReaction();
            $this->owner->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getBulletCapacity());
        });
    }

    public function doReaction(): void {
        if ($this->gun->getReaction() !== 0.0) {
            $playerPosition = $this->owner->getLocation();
            $dir = -$playerPosition->getYaw() - 90.0;
            $pitch = -$playerPosition->getPitch() - 180.0;
            $xd = $this->gun->getReaction() * $this->gun->getReaction() * cos(deg2rad($dir)) * cos(deg2rad($pitch)) / 6;
            $zd = $this->gun->getReaction() * $this->gun->getReaction() * -sin(deg2rad($dir)) * cos(deg2rad($pitch)) / 6;

            $vec = new Vector3($xd, 0, $zd);
            $vec->multiply(3);
            $this->owner->setMotion($vec);
        }
    }

    public function reload() {
        $remainingBullet = $this->getBulletAmount();

        if ($this->gun->getCurrentBullet() === $this->gun->getBulletCapacity()) {
            $this->owner->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getBulletCapacity());

        } else if ($remainingBullet === 0) {
            $this->owner->sendPopup("残弾がありません");

        } else {
            $this->playStartReloadingSound();
            $this->owner->sendPopup("リロード");
            $this->gun->reload($remainingBullet, function ($consumedBullets)  {
                $this->owner->getInventory()->removeItem(Item::get(BulletId::fromGunType($this->gun->getType()), 0, $consumedBullets));
            }, function () {
                $this->playEndReloadingSound();
                $this->owner->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getBulletCapacity());
            });
        }
    }

    protected function getBullets(): array {
        $inventoryContents = $this->owner->getInventory()->getContents();

        $bullets = array_filter($inventoryContents, function ($item) {
            if (is_subclass_of($item, "gun_system\pmmp\items\ItemBullet")) {
                return $item->getBullet()->getSupportType()->equal($this->gun->getType());

            }
            return false;
        });

        return $bullets;
    }

    protected function getBulletAmount(): int {
        $bullets = $this->getBullets();

        $bulletsAmount = array_sum(array_map(function ($bullet) {
            return $bullet->getCount();
        }, $bullets));

        return $bulletsAmount;
    }

    /**
     * @return Gun
     */
    public function getGunData(): Gun {
        return $this->gun;
    }
}