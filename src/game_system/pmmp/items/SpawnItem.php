<?php


namespace game_system\pmmp\items;


use pocketmine\item\Item;

class SpawnItem extends Item
{
    public const ITEM_ID = Item::EMERALD;

    public function __construct() {
        parent::__construct(self::ITEM_ID, 0, "SpawnItem");
    }
}