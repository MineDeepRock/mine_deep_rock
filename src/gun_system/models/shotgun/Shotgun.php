<?php


namespace gun_system\models\shotgun;


use Closure;
use gun_system\models\attachment\bullet\ShotgunBulletType;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\GunType;
use gun_system\models\ReloadDuration;
use gun_system\models\Response;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

abstract class Shotgun extends Gun
{
    private $bulletType;
    private $pellets;
    private $reloadTaskTaskHandler;

    public function __construct(ShotgunBulletType $bulletType,int $pellets, BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, int $bulletCapacity, float $reaction, ReloadDuration $reloadDuration, EffectiveRange $effectiveRange, GunPrecision $precision, TaskScheduler $scheduler) {
        $this->bulletType = $bulletType;
        $this->pellets = $pellets;
        parent::__construct(GunType::Shotgun(), $bulletDamage, $rate, $bulletSpeed, $bulletCapacity, $reaction, $reloadDuration, $effectiveRange, $precision, $scheduler);
    }

    public function cancelReloading() {
        $this->onReloading = false;
        if ($this->reloadTaskTaskHandler !== null)
            $this->reloadTaskTaskHandler->cancel();
    }

    public function tryShooting(Closure $onSucceed): Response {
        $this->cancelReloading();
        return parent::tryShooting($onSucceed);
    }

    public function tryShootingOnce(Closure $onSucceed): Response {
        $this->cancelReloading();
        return parent::tryShootingOnce($onSucceed);
    }

    /**
     * @return ShotgunBulletType
     */
    public function getBulletType(): ShotgunBulletType {
        return $this->bulletType;
    }


    protected function reload(int $remainingBullet,Closure $onPushed): void {
        $this->onReloading = true;

        $this->reloadTaskTaskHandler = $this->scheduler->scheduleRepeatingTask(new ClosureTask(
            function (int $currentTick) use ($remainingBullet,$onPushed): void {
                $this->currentBullet++;
                $onPushed();
                if ($this->currentBullet === $this->getBulletCapacity())
                    $this->cancelReloading();
            }
        ), 20 * 1 * $this->getReloadDuration()->getSecond());
    }

    /**
     * @return int
     */
    public function getPellets(): int {
        return $this->pellets;
    }
}