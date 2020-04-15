<?php


namespace gun_system\pmmp\items;


use gun_system\models\shotgun\Shotgun;
use gun_system\pmmp\entity\EntityBullet;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

abstract class ItemShotgun extends ItemGun
{

    public function shoot(Player $player, TaskScheduler $scheduler):void {
        if ($this->getBulletAmount($player) === 0) {
            $player->sendWhisper("GunSystem", "残弾がありません");
        } else {
            $message = $this->gun->shoot(function () use ($player, $scheduler) {
                $i = 0;
                while ($i < $this->gun->getPellets()) {
                    EntityBullet::spawn($player, $this->gun->getBulletSpeed()->getPerSecond(), $this->gun->getPrecision()->getValue(), $this->gun->getRange(), $scheduler);
                    $i++;
                }
                $this->doReaction($player);
            });

            if ($this->gun->getCurrentBullet() === 0)
                $this->reload($player);

            if ($message !== null)
                $player->sendWhisper("GunSystem", $message);
        }
    }

    public function __construct(int $id, string $name, ShotGun $gun) { parent::__construct($id, $name, $gun); }
}