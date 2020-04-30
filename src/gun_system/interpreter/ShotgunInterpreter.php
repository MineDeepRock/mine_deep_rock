<?php


namespace gun_system\interpreter;


use gun_system\models\shotgun\attachment\scope\IronSightForSG;
use gun_system\models\shotgun\attachment\scope\ShotgunScope;
use gun_system\models\shotgun\Shotgun;
use gun_system\pmmp\GunSounds;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class ShotgunInterpreter extends GunInterpreter
{
    private $scope;

    public function __construct(Shotgun $gun, Player $owner, TaskScheduler $scheduler) {
        $this->setScope(new IronSightForSG());
        parent::__construct($gun, $owner, $scheduler);
        $this->setWhenBecomeReady(function () {
            GunSounds::play($this->owner, GunSounds::ShotgunPumpAction());
        });
    }

    /**
     * @param ShotgunScope $scope
     */
    public function setScope(ShotgunScope $scope): void {
        $this->scope = $scope;
    }

    /**
     * @return ShotgunScope
     */
    public function getScope(): ShotgunScope {
        return $this->scope;
    }
}