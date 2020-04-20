<?php


namespace gun_system\pmmp\items;


use gun_system\models\shotgun\Shotgun;
use gun_system\pmmp\entity\EntityBullet;
use pocketmine\Player;

class ItemShotGun extends ItemGun
{
    public function __construct(string $name, Shotgun $gun) { parent::__construct($name, $gun); }

    protected function shootOnce(Player $player): void {
        $this->gun->shootOnce(function ($scheduler) use ($player) {
            $i = 0;
            while ($i < $this->gun->getPellets()) {
                EntityBullet::spawn($player, $this->gun->getBulletSpeed()->getPerSecond(), $this->gun->getPrecision()->getValue(), $scheduler);
                $i++;
            }
            $this->doReaction($player);
            $player->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getBulletCapacity());
            $this->playShootingSound($player);
        });
    }

    protected function shoot(Player $player): void {
        $this->gun->shoot(function ($scheduler) use ($player) {
            $i = 0;
            while ($i < $this->gun->getPellets()) {
                EntityBullet::spawn($player, $this->gun->getBulletSpeed()->getPerSecond(), $this->gun->getPrecision()->getValue(), $scheduler);
                $i++;
            }
            $this->doReaction($player);
            $player->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getBulletCapacity());
            $this->playShootingSound($player);
        });
    }
}