<?php


namespace gun_system\controller;


use Closure;
use gun_system\pmmp\GunSounds;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

class MagazineReloadingController extends ReloadingController
{
    private $second;

    public function __construct(Player $owner, int $magazineCapacity, float $second) {
        parent::__construct($owner, $magazineCapacity);
        $this->second = $second;
    }

    function carryOut(TaskScheduler $scheduler, int $inventoryBullets, Closure $reduceBulletFunc, Closure $onFinished): void {
        $this->onReloading = true;
        $empty = $this->magazineCapacity - $this->currentBullet;

        if ($empty > $inventoryBullets) {
            $this->currentBullet += $inventoryBullets;
            $reduceBulletFunc($inventoryBullets);
        } else {
            $this->currentBullet = $this->magazineCapacity;
            $reduceBulletFunc($empty);
        }

        GunSounds::play($this->owner, GunSounds::MagazineOut());
        $scheduler->scheduleDelayedTask(new ClosureTask(
            function (int $currentTick) use ($empty, $inventoryBullets, $onFinished): void {
                if ($empty > $inventoryBullets) {
                    $this->currentBullet += $inventoryBullets;
                } else {
                    $this->currentBullet = $this->magazineCapacity;
                }
                $this->onReloading = false;
                $onFinished();
                GunSounds::play($this->owner, GunSounds::MagazineIn());
            }
        ), 20 * $this->second);
    }

    function isCancelable(): bool {
        return false;
    }
}