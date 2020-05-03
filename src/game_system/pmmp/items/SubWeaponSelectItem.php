<?php


namespace game_system\pmmp\items;


use pocketmine\item\Item;

class SubWeaponSelectItem extends Item
{
    public const ITEM_ID = Item::STONE_SWORD;

    public function __construct() {
        parent::__construct(self::ITEM_ID, 0, "SubWeaponSelectItem");
    }
}