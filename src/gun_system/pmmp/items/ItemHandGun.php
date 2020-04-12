<?php


namespace gun_system\pmmp\items;


use gun_system\models\HandGun;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class ItemHandGun extends Item
{
    private $gun;
    public function __construct(int $id, string $name,TaskScheduler $scheduler) {
        $this->gun = new HandGun($scheduler);
        parent::__construct($id, 0, $name);
    }

    public function shoot(Player $player){
        $message = $this->gun->shoot();
        if ($message !== null)
            $player->sendWhisper("GunSystem",$message);
    }

    public function reload(Player $player){
        $this->gun->reload();
    }

}