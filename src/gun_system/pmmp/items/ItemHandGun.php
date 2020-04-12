<?php


namespace gun_system\pmmp\items;


use gun_system\models\HandGun;
use pocketmine\item\Item;
use pocketmine\Player;

class ItemHandGun extends Item
{
    private $gun;
    public function __construct(int $id, string $name) {
        $this->gun = new HandGun();
        parent::__construct($id, 0, $name);
    }

    public function shoot(Player $player){
        $this->gun->shoot();

        $player->sendWhisper("GunSystem","残弾:".$this->gun->getCurrentBullet());
    }

    public function reload(Player $player){
        $this->gun->reload();
    }

}