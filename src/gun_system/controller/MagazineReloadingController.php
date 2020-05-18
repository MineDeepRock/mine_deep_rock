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
    private $handler;

    public function __construct(Player $owner, int $magazineCapacity, float $second) {
        parent::__construct($owner, $magazineCapacity);
        $this->second = $second;
    }

    public function cancelReloading() {
        if ($this->handler !== null)
            $this->handler->cancel();
        $this->onReloading = false;
    }

    function carryOut(TaskScheduler $scheduler, int $inventoryBullets, Closure $reduceBulletFunc, Closure $onFinished): void {
        $this->onReloading = true;
        $empty = $this->magazineCapacity - $this->currentBullet;

        GunSounds::play($this->owner, GunSounds::MagazineOut());
        $this->handler = $scheduler->scheduleDelayedTask(new ClosureTask(
            function (int $currentTick) use ($empty, $inventoryBullets, $onFinished, $reduceBulletFunc): void {
                if ($empty > $inventoryBullets) {
                    $this->currentBullet += $inventoryBullets;
                    $reduceBulletFunc($inventoryBullets);
                } else {
                    $this->currentBullet += $empty;
                    $reduceBulletFunc($empty);
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