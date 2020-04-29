<?php


namespace gun_system\pmmp\client;


use Closure;
use gun_system\models\Gun;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class LightMachineGunClient extends GunClient
{
    private $onShoot;

    public function __construct(Player $owner, Gun $gun, Closure $onShoot) {
        $this->onShoot = $onShoot;
        parent::__construct($owner, $gun);
    }

    public function shoot(int $currentBullet, int $magazineCapacity, TaskScheduler $scheduler): void {
        ($this->onShoot)();
        parent::shoot($currentBullet, $magazineCapacity, $scheduler);
    }
}