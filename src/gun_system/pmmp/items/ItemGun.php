<?php


namespace gun_system\pmmp\items;


use gun_system\models\Gun;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

abstract class ItemGun extends Item
{
    protected $gun;

    public function __construct(int $id, string $name, Gun $gun) {
        $this->gun = $gun;
        parent::__construct($id, 0, $name);
    }

    public function shoot(Player $player,TaskScheduler $scheduler) {

        $message = $this->gun->shoot(function () use ($player,$scheduler) {
            Bullet::spawn($player, $this->gun->getBulletSpeed()->getPerSecond(), $this->gun->getPrecision()->getValue(), $this->gun->getRange(),$scheduler);
            $this->doReaction($player);
        });

        if ($message !== null)
            $player->sendWhisper("GunSystem", $message);
    }

    public function doReaction(Player $player): void {
        //TODO:バランス調整
        $playerPosition = $player->getLocation();
        $dir = -$playerPosition->getYaw() - 90.0;
        $pitch = -$playerPosition->getPitch() - 180.0;
        $xd = $this->gun->getReaction() *  cos(deg2rad($dir)) * cos(deg2rad($pitch)) / 6;
        $zd =  $this->gun->getReaction() * -sin(deg2rad($dir)) * cos(deg2rad($pitch)) / 6;

        $vec = new Vector3($xd, 0, $zd);
        $vec->multiply(3);
        $player->setMotion($vec);
    }

    public function reload(Player $player) {
        $this->gun->reload();
    }

    /**
     * @return Gun
     */
    public function getGunData(): Gun {
        return $this->gun;
    }
}