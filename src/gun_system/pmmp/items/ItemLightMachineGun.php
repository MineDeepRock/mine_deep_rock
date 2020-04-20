<?php


namespace gun_system\pmmp\items;


use gun_system\models\light_machine_gun\LightMachineGun;
use pocketmine\Player;

class ItemLightMachineGun extends ItemGun
{
    public function __construct(string $name, LightMachineGun $gun) { parent::__construct($name, $gun); }

    public function tryShootingOnce(?Player $player): bool {
        if ($this->gun->onOverheat()) {
            $player->sendPopup("オーバーヒート");
            $this->gun->doCoolDown();
            return false;
        }

        return parent::tryShootingOnce($player);
    }

    public function tryShooting(?Player $player): bool {
        if ($this->gun->onOverheat()) {
            $player->sendPopup("オーバーヒート");
            $this->gun->doCoolDown();
            return false;
        }
        return parent::tryShooting($player);
    }
}