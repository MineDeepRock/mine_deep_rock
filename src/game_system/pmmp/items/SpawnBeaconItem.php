<?php


namespace game_system\pmmp\items;


use pocketmine\item\Item;

class SpawnBeaconItem extends Item
{
    public const ITEM_ID = Item::BEACON;

    public function __construct() {
        parent::__construct(self::ITEM_ID, 0, "ビーコン");
        $this->setCustomName($this->getName());
    }
}