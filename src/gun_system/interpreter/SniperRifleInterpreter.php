<?php


namespace gun_system\interpreter;


use gun_system\models\sniper_rifle\attachment\scope\IronSightForSR;
use gun_system\models\sniper_rifle\attachment\scope\SniperRifleScope;
use gun_system\models\sniper_rifle\SniperRifle;
use gun_system\pmmp\GunSounds;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class SniperRifleInterpreter extends GunInterpreter
{
    private $scope;

    public function __construct(SniperRifle $gun, Player $owner, TaskScheduler $scheduler) {
        $this->setScope(new IronSightForSR());

        parent::__construct($gun, $owner, $scheduler);
        $this->setWhenBecomeReady(function () {
            GunSounds::play($this->owner, GunSounds::SniperRifleCocking());
        });
    }

    /**
     * @param SniperRifleScope $scope
     */
    public function setScope(SniperRifleScope $scope): void {
        $this->scope = $scope;
    }

    /**
     * @return SniperRifleScope
     */
    public function getScope(): SniperRifleScope {
        return $this->scope;
    }
}