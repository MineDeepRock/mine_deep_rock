<?php


namespace game_system\pmmp\items;


use pocketmine\item\Item;

class WeaponPurchaseItem extends Item
{
    public const ITEM_ID = Item::BOOK;

    public function __construct() {
        parent::__construct(self::ITEM_ID, 0, "WeaponPurchaseItem");
    }
}