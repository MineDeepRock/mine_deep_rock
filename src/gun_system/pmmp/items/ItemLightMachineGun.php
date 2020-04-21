<?php


namespace gun_system\pmmp\items;


use gun_system\models\light_machine_gun\LightMachineGun;
use gun_system\pmmp\GunSounds;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;

class ItemLightMachineGun extends ItemGun
{
    public function __construct(string $name, LightMachineGun $gun, Player $owner) {
        parent::__construct($name, $gun, $owner);
        $gun->setOnOverheated(function() use ($owner){
            $this->playOverheatSound();
            //TODO:バグ何故か表示されない
            $owner->sendPopup("オーバーヒート");
        });
        $gun->setOnFinishOverheat(function() use ($owner){
            $this->playReadySound();
            $this->owner->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getBulletCapacity());
        });
    }

    protected function shoot(): void {
        $this->playReadySound();

        parent::shoot();
    }

    private function playOverheatSound(): void {
        $soundName = GunSounds::LMGOverheat()->getText();
        $packet = new PlaySoundPacket();
        $packet->x = $this->owner->x;
        $packet->y = $this->owner->y;
        $packet->z = $this->owner->z;
        $packet->volume = 3;
        $packet->pitch = 2;
        $packet->soundName = $soundName;
        $this->owner->sendDataPacket($packet);
    }

    private function playReadySound(): void {
        $soundName = GunSounds::LMGReady()->getText();
        $packet = new PlaySoundPacket();
        $packet->x = $this->owner->x;
        $packet->y = $this->owner->y;
        $packet->z = $this->owner->z;
        $packet->volume = 3;
        $packet->pitch = 2;
        $packet->soundName = $soundName;
        $this->owner->sendDataPacket($packet);
    }
}