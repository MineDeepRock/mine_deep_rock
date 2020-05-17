<?php


namespace game_system\pmmp\items;


use pocketmine\item\Item;

class SandbagItem extends Item
{
    public const ITEM_ID = Item::SAND;

    public function __construct() {
        parent::__construct(self::ITEM_ID, 0, "土のう");
        $this->setCustomName($this->getName());
    }
}