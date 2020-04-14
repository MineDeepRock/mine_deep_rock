<?php


namespace gun_system\pmmp\items;


use gun_system\models\shotgun\Shotgun;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

abstract class ItemShotgun extends ItemGun
{

    public function shoot(Player $player, TaskScheduler $scheduler) {

        $message = $this->gun->shoot(function () use ($player, $scheduler) {
            $i = 0;
            while ($i < $this->gun->getPellets()) {
                Bullet::spawn($player, $this->gun->getBulletSpeed()->getValue(), $this->gun->getPrecision()->getValue(), $this->gun->getRange(), $scheduler);
                $i++;
            }
            $this->doReaction($player);
        });

        if ($message !== null)
            $player->sendWhisper("GunSystem", $message);
    }

    public function __construct(int $id, string $name, ShotGun $gun) { parent::__construct($id, $name, $gun); }
}