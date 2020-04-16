<?php


namespace gun_system\pmmp\items;


use gun_system\models\shotgun\Shotgun;
use gun_system\pmmp\entity\EntityBullet;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

abstract class ItemShotgun extends ItemGun
{

    public function shoot(Player $player, TaskScheduler $scheduler):bool {        if ($this->gun->isReloading()) {
        $player->sendPopup("リロード中");
        return false;
    }

        if ($this->getBulletAmount($player) === 0) {
            $player->sendPopup("残弾がありません");
            return false;
        }

        $result = $this->gun->shoot(function () use ($player, $scheduler) {
            $i = 0;
            while ($i < $this->gun->getPellets()) {
                EntityBullet::spawn($player, $this->gun->getBulletSpeed()->getPerSecond(), $this->gun->getPrecision()->getValue(), $this->gun->getRange(), $scheduler);
                $i++;
            }
        });

        if ($result)
            $player->sendPopup($this->gun->getCurrentBullet() . "\\" . $this->gun->getBulletCapacity());

        if ($this->gun->getCurrentBullet() === 0){
            //TODO:ここじゃない
            $player->sendPopup("リロード");
            $this->reload($player);
        }

        return true;
    }

    public function __construct(int $id, string $name, ShotGun $gun) { parent::__construct($id, $name, $gun); }
}