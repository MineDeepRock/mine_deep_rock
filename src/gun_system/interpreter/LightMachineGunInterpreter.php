<?php


namespace gun_system\interpreter;


use gun_system\models\light_machine_gun\attachment\scope\IronSightForLMG;
use gun_system\models\light_machine_gun\attachment\scope\LightMachineGunScope;
use gun_system\models\light_machine_gun\LightMachineGun;
use gun_system\models\light_machine_gun\OverheatGauge;
use gun_system\pmmp\client\LightMachineGunClient;
use gun_system\pmmp\GunSounds;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

class LightMachineGunInterpreter extends GunInterpreter
{
    private $scope;

    private $overheatGauge;
    private $isOverheat;

    public function __construct(LightMachineGun $gun, Player $owner, TaskScheduler $scheduler) {
        $this->setScope(new IronSightForLMG());
        parent::__construct($gun, $owner, $scheduler);

        $this->client = new LightMachineGunClient($this->owner,
            $this->gun,
            function () {
                $this->overheatGauge->raise($this->gun->getOverheatRate());
            });

        $this->overheatGauge = new OverheatGauge(function () {
            $this->cancelShooting();
            $this->isOverheat = true;
            $this->playOverheatSound();
            $this->owner->sendPopup("オーバーヒート");

            $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $currentTick): void {
                $this->isOverheat = false;
                $this->playReadySound();
                $this->owner->sendPopup($this->reloadingController->currentBullet . "\\" . $this->reloadingController->magazineCapacity);
                $this->overheatGauge->reset();
            }), 20 * 2);

        }, function () {
            $this->isOverheat = false;
        });


        if ($this->gun->getOverheatRate()->getPerShoot() !== 0) {
            $this->scheduler->scheduleRepeatingTask(new ClosureTask(function (int $currentTick): void {
                $this->overheatGauge->down(34);
            }), 20 * 1);
        }
    }

    /**
     * @param LightMachineGunScope $scope
     */
    public function setScope(LightMachineGunScope $scope): void {
        $this->scope = $scope;
    }

    /**
     * @return LightMachineGunScope
     */
    public function getScope(): LightMachineGunScope {
        return $this->scope;
    }

    private function playOverheatSound(): void {
        $soundName = GunSounds::LMGOverheat();
        GunSounds::play($this->owner, $soundName);
    }

    private function playReadySound(): void {
        $soundName = GunSounds::LMGReady();
        GunSounds::play($this->owner, $soundName);
    }

    public function tryShootOnce(): void {
        if ($this->isOverheat) {
            $this->owner->sendPopup("オーバーヒート中");
            return;
        }

        parent::tryShootOnce();
    }

    public function tryShoot(): void {
        if ($this->isOverheat) {
            $this->owner->sendPopup("オーバーヒート中");
            return;
        }

        parent::tryShoot();
    }

    public function tryReload(): void {
        if ($this->isOverheat) {
            $this->owner->sendPopup("オーバーヒート中");
            return;
        }

        parent::tryReload();
    }
}