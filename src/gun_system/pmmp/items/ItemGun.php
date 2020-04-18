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
    protected $gun;

    public function __construct(string $name, Gun $gun) {
        $this->gun = $gun;
        parent::__construct(ItemIds::BOW, 0, $name);
    }

    public function getMaxDurability(): int {
        return 100;
    }

    public function playShootingSound(Player $player): void {
        $soundName = GunSounds::shootSoundFromGunType($this->gun->getType())->getTypeText();
        $packet = new PlaySoundPacket();
        $packet->x = $player->x;
        $packet->y = $player->y;
        $packet->z = $player->z;
        $packet->volume = 3;
        $packet->pitch = 2;
        $packet->soundName = $soundName;
        $player->sendDataPacket($packet);
    }

    public function onReleaseUsing(Player $player): bool {
        $this->gun->cancelShooting();
        return true;
    }

    public function shootOnce(Player $player) {
        if ($this->gun->isReloading()) {
            $player->sendPopup("リロード中");
            return false;
        }

        if ($this->gun->getCurrentBullet() === 0) {
            $player->sendPopup("リロード");//TODO:ここじゃない
            $this->reload($player);
            return false;
        }
        $this->gun->shootOnce(function ($scheduler) use ($player) {
            $this->playShootingSound($player);
            EntityBullet::spawn($player, $this->gun->getBulletSpeed()->getPerSecond(), $this->gun->getPrecision()->getValue(), $this->gun->getRange(), $scheduler);
            $this->doReaction($player);
            $player->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getBulletCapacity());
        });

        return true;
    }

    public function shoot(Player $player): bool {
        if ($this->gun->isReloading()) {
            $player->sendPopup("リロード中");
            return false;
        }

        if ($this->gun->getCurrentBullet() === 0) {
            $this->reload($player);
            return false;
        }

        $this->gun->shoot(function ($scheduler) use ($player) {
            $this->playShootingSound($player);
            EntityBullet::spawn($player, $this->gun->getBulletSpeed()->getPerSecond(), $this->gun->getPrecision()->getValue(), $this->gun->getRange(), $scheduler);
            $this->doReaction($player);
            $player->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getBulletCapacity());
        });

        return true;
    }

    public function doReaction(Player $player): void {
        //TODO:バランス調整
        $playerPosition = $player->getLocation();
        $dir = -$playerPosition->getYaw() - 90.0;
        $pitch = -$playerPosition->getPitch() - 180.0;
        $xd = $this->gun->getReaction() * cos(deg2rad($dir)) * cos(deg2rad($pitch)) / 6;
        $zd = $this->gun->getReaction() * -sin(deg2rad($dir)) * cos(deg2rad($pitch)) / 6;

        $vec = new Vector3($xd, 0, $zd);
        $vec->multiply(3);
        $player->setMotion($vec);
    }

    public function reload(Player $player) {
        $remainingBullet = $this->getBulletAmount($player);

        if ($this->gun->getCurrentBullet() === $this->gun->getBulletCapacity()) {
            $player->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getBulletCapacity());

        } else if ($remainingBullet === 0) {
            $player->sendPopup("残弾がありません");

        } else {
            $player->sendPopup("リロード");
            $this->gun->reload($remainingBullet, function ($consumedBullets) use ($player) {
                $player->getInventory()->removeItem(Item::get(BulletId::fromGunType($this->gun->getType()), 0, $consumedBullets));
            }, function () use ($player) {
                $player->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getBulletCapacity());
            });
        }
    }

    protected function getBullets(Player $player): array {
        $inventoryContents = $player->getInventory()->getContents();

        $bullets = array_filter($inventoryContents, function ($item) {
            if (is_subclass_of($item, "gun_system\pmmp\items\ItemBullet")) {
                return $item->getBullet()->getSupportType()->equal($this->gun->getType());

            }
            return false;
        });

        return $bullets;
    }

    protected function getBulletAmount(Player $player): int {
        $bullets = $this->getBullets($player);

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