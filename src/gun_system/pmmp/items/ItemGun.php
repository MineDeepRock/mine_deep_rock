<?php


namespace gun_system\pmmp\items;


use gun_system\models\BulletId;
use gun_system\models\Gun;
use gun_system\models\GunType;
use gun_system\pmmp\entity\EntityBullet;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\Tool;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class ItemGun extends Tool
{
    protected $gun;

    public function __construct(string $name, Gun $gun) {
        $this->gun = $gun;
        parent::__construct(ItemIds::BOW, 0, $name);
    }

    public function getMaxDurability(): int {
        return 100;
    }

    public function shoot(Player $player, TaskScheduler $scheduler): bool {
        if ($this->gun->isReloading()) {
            $player->sendPopup("リロード中");
            return false;
        }

        if ($this->getBulletAmount($player) === 0) {
            $player->sendPopup("残弾がありません");
            return false;
        }

        if ($this->gun->getType() === GunType::Shotgun()) {
            $result = $this->gun->shoot(function () use ($player, $scheduler) {
                $i = 0;
                while ($i < $this->gun->getPellets()) {
                    EntityBullet::spawn($player, $this->gun->getBulletSpeed()->getPerSecond(), $this->gun->getPrecision()->getValue(), $this->gun->getRange(), $scheduler);
                    $i++;
                }
            });
        } else {
            $result = $this->gun->shoot(function () use ($player, $scheduler) {
                EntityBullet::spawn($player, $this->gun->getBulletSpeed()->getPerSecond(), $this->gun->getPrecision()->getValue(), $this->gun->getRange(), $scheduler);
                $this->doReaction($player);
            });
        }

        if ($result)
            $player->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getBulletCapacity());

        if ($this->gun->getCurrentBullet() === 0) {
            //TODO:ここじゃない
            $player->sendPopup("リロード");
            $this->reload($player);
        }

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
        if ($remainingBullet === 0) {
            $player->sendPopup("残弾がありません");

        } else {
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