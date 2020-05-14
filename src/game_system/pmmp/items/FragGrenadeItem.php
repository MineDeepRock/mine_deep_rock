<?php


namespace game_system\pmmp\items;


use pocketmine\item\Item;

class FragGrenadeItem extends Item
{
    public const ITEM_ID = Item::GHAST_TEAR;

    public function __construct() {
        parent::__construct(self::ITEM_ID, 0, "手榴弾");
        $this->setCustomName($this->getName());
    }
}