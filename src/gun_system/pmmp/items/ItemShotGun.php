<?php


namespace gun_system\pmmp\items;


use gun_system\models\GunType;
use gun_system\models\shotgun\Shotgun;
use gun_system\pmmp\entity\EntityBullet;
use pocketmine\Player;

class ItemShotGun extends ItemGun
{
    public function __construct(string $name, Shotgun $gun) { parent::__construct($name, $gun); }

    public function shoot(Player $player): bool {
        if ($this->gun->isReloading()) {
            $player->sendPopup("リロード中");
            return false;
        }

        if ($this->gun->getCurrentBullet() === 0) {
            $player->sendPopup("リロード");//TODO:ここじゃない
            $this->reload($player);

            return false;
        }


        $this->gun->shoot(function ($scheduler) use ($player) {
            $i = 0;
            while ($i < $this->gun->getPellets()) {
                EntityBullet::spawn($player, $this->gun->getBulletSpeed()->getPerSecond(), $this->gun->getPrecision()->getValue(), $this->gun->getRange(), $scheduler);
                $i++;
            }
        });

        return true;
    }
}