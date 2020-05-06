<?php


namespace game_system\pmmp\items;


use pocketmine\item\Item;

class SpawnFlareBoxItem extends Item
{
    public const ITEM_ID = Item::NETHER_WART;

    public function __construct() {
        parent::__construct(self::ITEM_ID, 0, "SpawnFlareBoxItem");
    }
}