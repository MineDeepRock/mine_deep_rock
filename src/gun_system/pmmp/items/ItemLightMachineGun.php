<?php


namespace gun_system\pmmp\items;


use gun_system\models\light_machine_gun\attachment\scope\LightMachineGunScope;
use gun_system\models\light_machine_gun\LightMachineGun;
use gun_system\pmmp\GunSounds;
use pocketmine\Player;

class ItemLightMachineGun extends ItemGun
{
    public function __construct(string $name, LightMachineGun $gun, Player $owner) {
        parent::__construct($name, $gun, $owner);
        $gun->setOnOverheated(function () use ($owner) {
            $this->playOverheatSound();
            //TODO:バグ何故か表示されない
            $owner->sendPopup("オーバーヒート");
        });
        $gun->setOnFinishOverheat(function () use ($owner) {
            $this->playReadySound();
            $this->owner->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getMagazineCapacity());
        });
    }

    public function setScope(LightMachineGunScope $scope): void {
        $this->gun->setScope($scope);
    }

    private function playOverheatSound(): void {
        $soundName = GunSounds::LMGOverheat()->getText();
        GunSounds::play($this->owner,$soundName);
    }

    private function playReadySound(): void {
        $soundName = GunSounds::LMGReady()->getText();
        GunSounds::play($this->owner,$soundName);
    }
}