<?php


namespace gun_system\interpreter;


use Closure;
use gun_system\controller\ClipReloadingController;
use gun_system\controller\MagazineReloadingController;
use gun_system\controller\OneByOneReloadingController;
use gun_system\controller\OverheatController;
use gun_system\controller\ShootingController;
use gun_system\models\BulletId;
use gun_system\models\ClipReloadingType;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunType;
use gun_system\models\MagazineReloadingType;
use gun_system\models\OneByOneReloadingType;
use gun_system\models\shotgun\Shotgun;
use gun_system\pmmp\client\GunClient;
use gun_system\pmmp\GunSounds;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

abstract class GunInterpreter
{
    protected $client;

    protected $scheduler;

    protected $owner;
    protected $gun;
    protected $reloadingController;
    protected $shootingController;
    protected $overheatController;

    private $isADS;

    public function __construct(Gun $gun, Player $owner, TaskScheduler $scheduler) {
        $this->scheduler = $scheduler;
        $this->owner = $owner;
        $this->gun = $gun;
        $reloadingType = $this->gun->getReloadingType();

        if ($reloadingType instanceof MagazineReloadingType) {
            $this->reloadingController = new MagazineReloadingController($this->owner, $reloadingType->magazineCapacity, $reloadingType->second);
        } else if ($reloadingType instanceof ClipReloadingType) {
            $this->reloadingController = new ClipReloadingController($this->owner, $reloadingType->magazineCapacity, $reloadingType->clipCapacity, $reloadingType->secondOfClip, $reloadingType->secondOfOne);
        } else if ($reloadingType instanceof OneByOneReloadingType) {
            $this->reloadingController = new OneByOneReloadingController($this->owner, $reloadingType->magazineCapacity, $reloadingType->second);
        }

        $this->shootingController = new ShootingController($gun->getType(), $gun->getRate(), function ($value): int {
            $this->reloadingController->currentBullet -= $value;
            return $this->reloadingController->currentBullet;
        }, $scheduler);

        $this->overheatController = new OverheatController(
            $gun->getOverheatRate(),
            function(){
                $this->cancelShooting();
                GunSounds::play($this->owner, GunSounds::LMGOverheat());
                $this->owner->sendPopup("オーバーヒート");
            },
            function(){
                GunSounds::play($this->owner, GunSounds::LMGReady());
                $this->owner->sendPopup($this->reloadingController->currentBullet . "\\" . $this->reloadingController->magazineCapacity);
            },
            $this->scheduler);

        $this->client = new GunClient($this->owner, $this->gun);
    }

    public function setWhenBecomeReady(Closure $whenBecomeReady): void {
        $this->shootingController->whenBecomeReady = $whenBecomeReady;
    }

    public function aim(): void {
        $this->isADS = true;
    }

    public function cancelAim(): void {
        $this->isADS = false;
    }

    public function scare(Closure $onFinished): void {
        $this->gun->setPrecision(new GunPrecision($this->gun->getPrecision()->getADS() - 3, $this->gun->getPrecision()->getHipShooting() - 3));
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $currentTick): void {
            $this->gun->setPrecision(new GunPrecision($this->gun->getPrecision()->getADS() + 3, $this->gun->getPrecision()->getHipShooting() + 3));
        }), 20 * 3);
        $this->client->scare($this->scheduler,$onFinished);
    }

    public function cancelShooting(): void {
        $this->shootingController->cancelShooting();
    }

    public function tryShootOnce() {
        if ($this->reloadingController->isCancelable())
            $this->reloadingController->cancelReloading();

        if ($this->reloadingController->isReloading()) {
            $this->owner->sendPopup("リロード中");
            return;
        }

        if ($this->reloadingController->isEmpty()) {
            $this->owner->sendPopup("マガジンに弾がありません");
            return;
        }

        if ($this->reloadingController->isReloading()) {
            $this->owner->sendPopup($this->reloadingController->currentBullet . "\\" . $this->reloadingController->magazineCapacity);
            return;
        }

        if ($this->shootingController->onCoolTime()) {
            return;
        }

        if ($this->overheatController->isOverheat()) {
            $this->owner->sendPopup("オーバーヒート中");
            return;
        }

        $this->shootingController->shootOnce(function (): void {
            $this->overheatController->raise();
            $this->client->shoot($this->reloadingController->currentBullet, $this->reloadingController->magazineCapacity, $this->scheduler);
        });
    }

    public function tryShoot(): void {
        if ($this->reloadingController->isCancelable())
            $this->reloadingController->cancelReloading();

        if ($this->reloadingController->isReloading()) {
            $this->owner->sendPopup("リロード中");
            return;
        }

        if ($this->reloadingController->isEmpty()) {
            $this->owner->sendPopup("マガジンに弾がありません");
            return;
        }

        if ($this->overheatController->isOverheat()) {
            $this->owner->sendPopup("オーバーヒート中");
            return;
        }

        if ($this->shootingController->onCoolTime()) {
            //TODO: 1/rate - (now-lastShootDate)
            $this->shootingController->delayShoot(1 / $this->gun->getRate()->getPerSecond(), function (): void {
                $this->client->shoot($this->reloadingController->currentBullet, $this->reloadingController->magazineCapacity, $this->scheduler);
            });
            $this->owner->sendPopup($this->reloadingController->currentBullet . "\\" . $this->reloadingController->magazineCapacity);
            return;
        }

        if ($this->gun->getType()->equal(GunType::LMG()) && !$this->isADS) {
            $this->shootingController->delayShoot(1 / $this->gun->getRate()->getPerSecond(), function (): void {
                $this->client->shoot($this->reloadingController->currentBullet, $this->reloadingController->magazineCapacity, $this->scheduler);
            });
        }
        $this->shootingController->shoot(function (): void {
            $this->overheatController->raise();
            $this->client->shoot($this->reloadingController->currentBullet, $this->reloadingController->magazineCapacity, $this->scheduler);
        });
    }

    public function tryReload(): void {
        $inventoryBullets = $this->getBulletAmount();

        if ($this->reloadingController->isReloading()) {
            $this->owner->sendPopup("リロード中");
            return;
        }

        if ($this->reloadingController->isFull()) {
            $this->owner->sendPopup("Max");
            return;
        }

        if ($inventoryBullets === 0) {
            $this->owner->sendPopup("弾薬がありません");
            return;
        }

        if ($this->overheatController->isOverheat()) {
            $this->owner->sendPopup("オーバーヒート中");
            return;
        }
        
        $this->cancelShooting();

        $reduceBulletFunc = function ($value): int {
            $this->owner->sendPopup("リロード");
            if ($this->gun instanceof Shotgun) {
                $this->owner->getInventory()->removeItem(Item::get(BulletId::fromGunType($this->gun->getType(), $this->gun->getBulletType()), 0, $value));
            } else {
                $this->owner->getInventory()->removeItem(Item::get(BulletId::fromGunType($this->gun->getType()), 0, $value));
            }
            return $this->getBulletAmount();
        };

        $onFinishedReloading = function (): void {
            $this->owner->sendPopup($this->reloadingController->currentBullet . "/" . $this->reloadingController->magazineCapacity);
        };

        $this->reloadingController->carryOut($this->scheduler, $inventoryBullets, $reduceBulletFunc, $onFinishedReloading);
    }

    protected function getBullets(): array {
        $inventoryContents = $this->owner->getInventory()->getContents();

        $bullets = array_filter($inventoryContents, function ($item) {
            if (is_subclass_of($item, "gun_system\pmmp\items\bullet\ItemBullet")) {
                if ($this->gun->getType()->equal(GunType::Shotgun())) {
                    return $item->getBullet()->getSupportGunType()->equal($this->gun->getType())
                        && $item->getBullet()->getBulletType()->equal($this->gun->getBulletType());
                } else {
                    return $item->getBullet()->getSupportGunType()->equal($this->gun->getType());
                }
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