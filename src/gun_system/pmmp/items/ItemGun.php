<?php


namespace gun_system\pmmp\items;


use gun_system\models\Gun;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

abstract class ItemGun extends Item
{
    private $gun;

    public function __construct(int $id, string $name, Gun $gun) {
        $this->gun = $gun;
        parent::__construct($id, 0, $name);
    }

    public function shoot(Player $player) {

        $message = $this->gun->shoot(function () use ($player) {
            Bullet::spawn($player, $this->gun->getBulletSpeed()->getValue(), $this->gun->getPrecision()->getValue());
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
        $xd = cos(deg2rad($dir)) * cos(deg2rad($pitch));
        $yd = sin(deg2rad($pitch));
        $zd = -sin(deg2rad($dir)) * cos(deg2rad($pitch));

        $vec = new Vector3($xd, $yd, $zd);
        $vec->multiply($this->gun->getReaction() / 3);
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