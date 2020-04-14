<?php


namespace gun_system\pmmp\items;


use gun_system\models\shotgun\Shotgun;
use pocketmine\Player;

abstract class ItemShotgun extends ItemGun
{

    public function shoot(Player $player) {

        $message = $this->gun->shoot(function () use ($player) {
            $i = 0;
            while ($i < $this->gun->getPellets()) {
                Bullet::spawn($player, $this->gun->getBulletSpeed()->getValue(), $this->gun->getPrecision()->getValue());
                $i++;
            }
            $this->doReaction($player);
        });

        if ($message !== null)
            $player->sendWhisper("GunSystem", $message);
    }

    public function __construct(int $id, string $name, ShotGun $gun) { parent::__construct($id, $name, $gun);}
}