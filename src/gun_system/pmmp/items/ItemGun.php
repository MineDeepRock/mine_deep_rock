<?php


namespace gun_system\pmmp\items;


use gun_system\models\Gun;
use pocketmine\item\Item;
use pocketmine\Player;

abstract class ItemGun extends Item
{
    private $gun;

    public function __construct(int $id, string $name,Gun $gun) {
        $this->gun = $gun;
        parent::__construct($id, 0, $name);
    }

    public function shoot(Player $player) {

        $message = $this->gun->shoot(function () use ($player) {
            Bullet::spawn($player);
        });

        if ($message !== null)
            $player->sendWhisper("GunSystem", $message);
    }

    public function reload(Player $player) {
        $this->gun->reload();
    }
}