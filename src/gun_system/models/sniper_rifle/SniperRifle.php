<?php


namespace gun_system\models\sniper_rifle;


use Closure;
use gun_system\models\BulletDamage;
use gun_system\models\BulletSpeed;
use gun_system\models\EffectiveRange;
use gun_system\models\Gun;
use gun_system\models\GunPrecision;
use gun_system\models\GunRate;
use gun_system\models\GunType;
use gun_system\models\ReloadDuration;
use gun_system\models\Response;
use gun_system\models\shotgun\attachment\scope\IronSightForSG;
use gun_system\models\shotgun\attachment\scope\ShotgunScope;
use gun_system\models\sniper_rifle\attachment\scope\IronSightForSR;
use gun_system\models\sniper_rifle\attachment\scope\SniperRifleScope;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

class SniperRifle extends Gun
{
    private $scope;
    private $reloadTaskTaskHandler;

    public function __construct(BulletDamage $bulletDamage, GunRate $rate, BulletSpeed $bulletSpeed, int $bulletCapacity, float $reaction, ReloadDuration $reloadDuration, EffectiveRange $effectiveRange, GunPrecision $precision, TaskScheduler $scheduler) {
        $this->setScope(new IronSightForSR());

        parent::__construct(GunType::SniperRifle(), $bulletDamage, $rate, $bulletSpeed, $bulletCapacity, $reaction, $reloadDuration, $effectiveRange, $precision, $scheduler);
    }

    /**
     * @return SniperRifleScope
     */
    public function getScope(): SniperRifleScope {
        return $this->scope;
    }

    /**
     * @param SniperRifleScope $scope
     */
    public function setScope(SniperRifleScope $scope): void {
        $this->scope = $scope;
    }

    public function tryShooting(Closure $onSucceed,bool $isADS): Response {
        $this->cancelReloading();
        return parent::tryShooting($onSucceed,$isADS);
    }

    public function tryShootingOnce(Closure $onSucceed): Response {
        $this->cancelReloading();
        return parent::tryShootingOnce($onSucceed);
    }

    public function cancelReloading() {
        $this->onReloading = false;
        if ($this->reloadTaskTaskHandler !== null)
            $this->reloadTaskTaskHandler->cancel();
    }

    protected function reload(int $remainingBullet, Closure $onPushed): void {
        $this->onReloading = true;

        $this->reloadTaskTaskHandler = $this->scheduler->scheduleRepeatingTask(new ClosureTask(
            function (int $currentTick) use ($remainingBullet, $onPushed): void {
                $bunch = intval(($this->bulletCapacity - $this->currentBullet) / 5);

                if ($bunch > 0 && $remainingBullet >= 5) {
                    $this->currentBullet += 5;
                    $onPushed();
                    if ($this->currentBullet === $this->getBulletCapacity())
                        $this->cancelReloading();
                } else {
                    $this->currentBullet++;
                    $onPushed();
                    if ($this->currentBullet === $this->getBulletCapacity())
                        $this->cancelReloading();
                }
            }
        ), 20 * 1 * $this->getReloadDuration()->getSecond());
    }
}